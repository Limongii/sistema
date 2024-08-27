<?php
session_start();
include 'config.php';

// Verifique se o usuário está logado e obtenha o nome do usuário
$usuario_nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : '';

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
    <script>
        // Função para obter a localização
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocalização não é suportada por este navegador.");
            }
        }

        function showPosition(position) {
            // Preenche os campos ocultos com latitude e longitude
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        }

        // Chama a função para obter a localização ao carregar a página
        window.onload = getLocation;

        // Função para formatar a placa
        function formatPlaca(input) {
            // Remove qualquer caractere que não seja letra ou número
            input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            // Verifica se já existem 3 caracteres
            if (input.value.length > 3 && input.value[3] !== '-') {
                // Insere o separador "-"
                input.value = input.value.slice(0, 3) + '-' + input.value.slice(3);
            }

            // Limita a entrada total a 8 caracteres (3 letras + "-" + 4 números)
            if (input.value.length > 8) {
                input.value = input.value.slice(0, 8);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Motorista</h1>
        <form action="processa_formulario.php" method="post" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" readonly><br><br>

            <label for="placa">Placa:</label>
            <input type="text" id="placa" name="placa" required maxlength="8" oninput="formatPlaca(this)"><br><br>

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

            <!-- Campos ocultos para latitude e longitude -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <!-- Container para botões -->
            <div class="button-container">
                <input type="submit" value="Enviar">
                <button type="button" onclick="history.back()">Voltar</button>
            </div>
        </form>
    </div>
</body>
</html>
