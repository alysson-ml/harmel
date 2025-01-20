<?php
header('Content-Type: application/json');

include 'db.php';

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Conexão com o banco de dados falhou: " . $conn->connect_error]);
    exit();
}

$sql = "SELECT * FROM produto";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Erro ao executar a consulta: " . $conn->error]);
    exit();
}

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}

echo json_encode($produtos);
$conn->close();
?>