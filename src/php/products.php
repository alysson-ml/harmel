<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM produto";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(array('status' => 'error', 'message' => 'Erro na consulta: ' . $conn->error));
        exit();
    }

    $produtos = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
    }

    $conn->close();

    echo json_encode($produtos);
} catch (Exception $e) {
    echo json_encode(array('status' => 'error', 'message' => 'Erro: ' . $e->getMessage()));
}
?>