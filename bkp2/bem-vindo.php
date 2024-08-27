<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="welcome-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>

        <div class="button-container">
            <a href="formulario.php" class="button">Formulário</a>
            <a href="pagina2.php" class="button">Função 2</a>
            <a href="pagina3.php" class="button">Função 3</a>
            <a href="pagina4.php" class="button">Função 4</a>
        </div>
        
        <a href="logout.php" class="button button-logout">Sair</a>
    </div>
</body>
</html>
