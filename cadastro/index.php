<?php
session_start();
include 'config.php';

// Verifica se o usuário está logado e tem nível 2
if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 2) {
    header("Location: acesso-negado.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclua o CSS separado -->
</head>
<body>
    <div class="form-container">
        <h1>Cadastro de Usuário</h1>
        <form action="processa_cadastro.php" method="post">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="nivel">Nível:</label>
            <select id="nivel" name="nivel" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <div class="button-container">
                <a href="../painel/pagina2.php" class="btn btn-back">Voltar</a>
                <input type="submit" value="Cadastrar" class="btn btn-submit">
            </div>
        </form>
    </div>
</body>
</html>
