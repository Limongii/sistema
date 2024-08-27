<?php
include 'config.php'; // Inclua o arquivo de configuração do banco de dados

// Processa o formulário quando é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $nivel = $_POST['nivel'];

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        echo "As senhas não coincidem.";
        exit;
    }

    // Faz a hash da senha
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    // Prepara e executa a inserção
    $sql = "INSERT INTO usuarios (usuario, nome, email, senha, nivel) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $usuario, $nome, $email, $senha_hash, $nivel);

    if ($stmt->execute()) {
        echo "<script>
                alert('Cadastro realizado com sucesso!');
                window.location.href = 'index.php'; // Redireciona para a página de login
              </script>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
