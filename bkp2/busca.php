<?php
include '../config.php';

// Obtém os nomes distintos para o menu suspenso
$nomes = [];
$sql = "SELECT DISTINCT nome FROM motorista";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nomes[] = $row['nome'];
    }
}

// Se um nome foi selecionado, obtém os dados associados a esse nome
$dados_motoristas = [];
if (isset($_GET['nome'])) {
    $nome_selecionado = $_GET['nome'];
    $sql = "SELECT placa, hodometro, imagem FROM motorista WHERE nome = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $nome_selecionado);
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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Motoristas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Buscar Motoristas</h1>
    <form method="get" action="busca.php">
        <label for="nome">Selecione um nome:</label>
        <select id="nome" name="nome" onchange="this.form.submit()">
            <option value="">Selecione um nome</option>
            <?php foreach ($nomes as $nome): ?>
                <option value="<?php echo htmlspecialchars($nome); ?>" <?php echo isset($_GET['nome']) && $_GET['nome'] === $nome ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($nome); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($dados_motoristas)): ?>
        <table>
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Hodômetro</th>
                    <th>Imagem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_motoristas as $motorista): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($motorista['placa']); ?></td>
                        <td><?php echo htmlspecialchars($motorista['hodometro']); ?></td>
                        <td>
                            <?php if ($motorista['imagem']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($motorista['imagem']); ?>" alt="Imagem do motorista" class="img-thumbnail">
                            <?php else: ?>
                                Sem imagem
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['nome'])): ?>
        <p>Nenhum dado encontrado para o nome selecionado.</p>
    <?php endif; ?>
</body>
</html>
