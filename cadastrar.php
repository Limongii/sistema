<?php
include 'config.php';

$usuario = 'limongi';
$senha = '36110069';
$hash_senha = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
$stmt->bind_param("ss", $usuario, $hash_senha);
$stmt->execute();
$stmt->close();
$conn->close();
?>