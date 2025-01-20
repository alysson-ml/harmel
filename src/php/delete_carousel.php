<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_carrossel = $_GET['id'];

    // Consulta imagem para remover do diretório
    $sql = "SELECT imagem FROM carrossel WHERE id_carrossel = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carrossel);
    $stmt->execute();
    $stmt->bind_result($imagem);
    $stmt->fetch();
    $stmt->close();

    // Exclui a imagem do diretório
    $file = '../../assets/images/' . $imagem;
    if (file_exists($file)) {
        unlink($file);
    }

    // Exclui a imagem do banco de dados
    $sql = "DELETE FROM carrossel WHERE id_carrossel = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carrossel);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php?status=success");
    exit();
} else {
    header("Location: admin.php?status=error");
    exit();
}

//$conn->close(); se der erro remover as //
?>