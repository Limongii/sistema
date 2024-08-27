<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Prepara a consulta SQL para prevenir SQL Injection
    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hash_senha);
    $stmt->fetch();

    // Verifica se o usuário existe e a senha é válida
    if ($stmt->num_rows > 0 && password_verify($senha, $hash_senha)) {
        $_SESSION['usuario'] = $usuario;
        header("Location: bem-vindo.php");
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <form method="post" action="">
            <h2>Login</h2>
            <?php if (isset($erro)): ?>
                <p class="error"><?php echo $erro; ?></p>
            <?php endif; ?>
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>