<?php
include 'config.php';
include 'nivel.php';

// Verifique se o usuário está logado e obtenha o nome do usuário
$usuario_nome = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';

// Obtém os dados para o menu suspenso
$postos = [];
$sql = "SELECT id, nome FROM posto";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $postos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Motorista</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Motorista</h1>
        <form action="processa_formulario.php" method="post" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" readonly><br><br>

            <label for="placa">Placa:</label>
            <input type="text" id="placa" name="placa" required><br><br>

            <label for="hodometro">Hodômetro:</label>
            <input type="number" id="hodometro" name="hodometro" required><br><br>

            <label for="posto">Posto:</label>
            <select id="posto" name="posto" required>
                <option value="">Selecione um posto</option>
                <?php foreach ($postos as $posto): ?>
                    <option value="<?php echo htmlspecialchars($posto['id']); ?>">
                        <?php echo htmlspecialchars($posto['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="imagem">Imagem:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

            <!-- Campos ocultos para data e hora -->
            <input type="hidden" id="data" name="data" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" id="hora" name="hora" value="<?php echo date('H:i:s'); ?>">

            <!-- Container para botões -->
            <div class="button-container">
                <input type="submit" value="Enviar">
                <button type="button" onclick="history.back()">Voltar</button>
            </div>
        </form>
    </div>
</body>
</html>
