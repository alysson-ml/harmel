<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "harmel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Mensagem de sucesso
//echo "Conexão com o banco de dados estabelecida com sucesso!";
?>