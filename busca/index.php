<?php
include '../config.php';

// Verifica o nível do usuário
session_start();
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 2) {
    header("Location: acesso_negado.php");
    exit();
}

// Inicializa variáveis
$nomes = [];
$dados_motoristas = [];

// Obtém os nomes distintos para o menu suspenso
$sql = "SELECT DISTINCT nome FROM motorista WHERE nome IS NOT NULL AND nome <> ''";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nomes[] = $row['nome'];
    }
} else {
    echo "Nenhum motorista encontrado na base de dados.";
}

// Inicializa a consulta base
$sql = "SELECT placa, hodometro, imagem, data, hora, latitude, longitude FROM motorista WHERE 1=1";

// Adiciona filtro pelo nome, se selecionado
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $nome_selecionado = trim($_GET['nome']);
    $sql .= " AND nome = ?";
}

// Adiciona filtro de datas, se definidos
if (isset($_GET['data_inicio']) && isset($_GET['data_fim']) && !empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    $sql .= " AND CONCAT(data, ' ', hora) BETWEEN ? AND ?";
}

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);

if ($stmt) {
    $params = [];
    $types = '';

    // Adiciona parâmetros de filtro pelo nome
    if (isset($_GET['nome']) && !empty($_GET['nome'])) {
        $params[] = $nome_selecionado;
        $types .= 's';
    }

    // Adiciona parâmetros de filtro de data
    if (isset($_GET['data_inicio']) && isset($_GET['data_fim']) && !empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
        $params[] = $data_inicio . ' 00:00:00'; // Hora de início
        $params[] = $data_fim . ' 23:59:59';   // Hora de fim
        $types .= 'ss';
    }

    if ($types) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados_motoristas[] = $row;
        }
    }
    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Motoristas</title>
    <link rel="stylesheet" href="busca.css">
</head>
<body>
    <div class="container">
        <h1>Buscar Motoristas</h1>

        <!-- Formulário de filtro -->
        <form method="get" action="index.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <select id="nome" name="nome" class="input-field" onchange="this.form.submit()">
                        <option value="">Selecione um nome</option>
                        <?php foreach ($nomes as $nome): ?>
                            <option value="<?php echo htmlspecialchars($nome); ?>" <?php echo isset($_GET['nome']) && $_GET['nome'] === $nome ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nome); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_inicio">Data Início:</label>
                    <input type="date" id="data_inicio" name="data_inicio" class="input-field" value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="data_fim">Data Fim:</label>
                    <input type="date" id="data_fim" name="data_fim" class="input-field" value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>">
                </div>
            </div>

            <div class="form-actions">
                <input type="submit" value="Filtrar" class="btn-filtrar">
                <a href="../painel/pagina2.php" class="btn-voltar">Voltar</a>
                <a href="exportar_excel.php?nome=<?php echo isset($_GET['nome']) ? urlencode($_GET['nome']) : ''; ?>&data_inicio=<?php echo isset($_GET['data_inicio']) ? urlencode($_GET['data_inicio']) : ''; ?>&data_fim=<?php echo isset($_GET['data_fim']) ? urlencode($_GET['data_fim']) : ''; ?>" class="btn-excel">
                    <img src="../icons/excel-icon.png" alt="Exportar para Excel">
                </a>
            </div>
        </form>

        <?php if (!empty($dados_motoristas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Hodômetro</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Imagem</th>
                        <th>Localização</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados_motoristas as $motorista): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($motorista['placa']); ?></td>
                            <td><?php echo htmlspecialchars($motorista['hodometro']); ?></td>
                            <td><?php echo htmlspecialchars($motorista['data']); ?></td>
                            <td><?php echo htmlspecialchars($motorista['hora']); ?></td>
                            <td>
                                <?php if ($motorista['imagem']): ?>
                                    <img src="../painel/uploads/<?php echo htmlspecialchars($motorista['imagem']); ?>" alt="Imagem do motorista" class="img-thumbnail" style="cursor: pointer;" onclick="openModal(this.src)">
                                <?php else: ?>
                                    Sem imagem
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($motorista['latitude'] && $motorista['longitude']): ?>
                                    <button onclick="window.open('https://www.google.com/maps?q=<?php echo htmlspecialchars($motorista['latitude']); ?>,<?php echo htmlspecialchars($motorista['longitude']); ?>', '_blank')" class="btn-maps">Ver no Google Maps</button>
                                <?php else: ?>
                                    Sem localização
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_GET['nome']) || isset($_GET['data_inicio']) || isset($_GET['data_fim'])): ?>
            <p>Nenhum dado encontrado com os filtros selecionados.</p>
        <?php endif; ?>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Imagem do motorista">
            </div>
        </div>

        <script>
            function openModal(src) {
                document.getElementById('modalImage').src = src;
                document.getElementById('myModal').style.display = 'block';
            }

            function closeModal() {
                document.getElementById('myModal').style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target === document.getElementById('myModal')) {
                    closeModal();
                }
            }
        </script>
    </div>
</body>
</html>
