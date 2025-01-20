<?php
include 'db.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $imagem = $_FILES['imagem']['name'];

    // Move o arquivo da imagem para o diretório desejado
    $target = '../../assets/images/' . basename($imagem);
    move_uploaded_file($_FILES['imagem']['tmp_name'], $target);

    // Insere a nova imagem no banco de dados
    $sql = "INSERT INTO carrossel (nome, imagem) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nome, $imagem);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php?status=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Imagem ao Carrossel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Adicionar Imagem ao Carrossel</h1>
        <form action="add_carousel.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Adicionar</button>
        </form>
    </div>
</body>
</html>