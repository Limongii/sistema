<?php
session_start();
include 'config.php';

// Verifique se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtém o nível do usuário a partir da sessão
$usuario_id = $_SESSION['usuario'];
$sql = "SELECT nivel FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario);
$stmt->execute();
$stmt->bind_result($nivel);
$stmt->fetch();
$stmt->close();

// Define o nível necessário para acessar a página
$nivel_necessario = 1; // Por exemplo, nível 2 é necessário

if ($nivel > $nivel_necessario) {
    header('Location: naoautorizado.php');
    exit();
}

?>