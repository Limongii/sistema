<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Prepara a consulta SQL para prevenir SQL Injection
    $stmt = $conn->prepare("SELECT senha, nivel, nome FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hash_senha, $nivel, $nome);
    $stmt->fetch();

    // Verifica se o usuário existe e a senha é válida
    if ($stmt->num_rows > 0 && password_verify($senha, $hash_senha)) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nivel'] = $nivel; // Armazena o nível do usuário na sessão
        $_SESSION['nome'] = $nome; // Armazena o nome do usuário na sessão

        // Redireciona baseado no nível do usuário
        if ($nivel == 1) {
            header("Location: painel/pagina1.php"); // Redireciona para a página para nível 1
        } elseif ($nivel == 2) {
            header("Location: painel/pagina2.php"); // Redireciona para a página para nível 2
        } else {
            header("Location: painel/pagina1.php"); // Página padrão caso o nível não seja 1 ou 2
        }
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles_login.css">
</head>
<body>
    <div class="page">
        <form method="POST" class="formLogin">
            <h1>Login</h1>
            <p>Digite os seus dados de acesso no campo abaixo.</p>
            <?php if (isset($erro)): ?>
                <p class="error"><?php echo htmlspecialchars($erro); ?></p>
            <?php endif; ?>
            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" autofocus="true" required />
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />
            <a href="recuperar-senha.php">Esqueci minha senha</a>
            <input type="submit" value="Acessar" class="btn" />
        </form>
    </div>
</body>
</html>
