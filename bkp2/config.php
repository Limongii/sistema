<?php
$servername = "localhost";
$username = "root"; // seu usuário do MySQL
$password = ""; // sua senha do MySQL
$dbname = "teste";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
