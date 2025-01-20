<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT imagem FROM produto WHERE id_produto='$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $imagem = $product['imagem'];
        
        $sql = "DELETE FROM produto WHERE id_produto='$id'";
        
        if ($conn->query($sql) === TRUE) {
            if ($imagem && file_exists('../../assets/images/' . $imagem)) {
                unlink('../../assets/images/' . $imagem);
            }
            header("Location: admin.php?status=success");
        } else {
            header("Location: admin.php?status=error");
        }
    } else {
        header("Location: admin.php?status=error");
    }
    $conn->close();
    exit();
} else {
    header("Location: admin.php?status=error");
    exit();
}
?>