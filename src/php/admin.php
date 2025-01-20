<?php
include 'db.php';

// Consulta produtos
$sqlProdutos = "SELECT * FROM produto";
$resultProdutos = $conn->query($sqlProdutos);
$products = [];

if ($resultProdutos->num_rows > 0) {
    while ($row = $resultProdutos->fetch_assoc()) {
        $products[] = $row;
    }
}

// Consulta carrossel
$sqlCarrossel = "SELECT id_carrossel, nome, imagem FROM carrossel";
$resultCarrossel = $conn->query($sqlCarrossel);
$carrosselImagens = [];

if ($resultCarrossel->num_rows > 0) {
    while ($row = $resultCarrossel->fetch_assoc()) {
        $carrosselImagens[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Produtos</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Harmel</a>
            <div class="d-flex">
                <a href="admin_users.php" class="btn btn-primary mx-2">Gerenciar Usuários</a>
                <a href="admin_orders.php" class="btn btn-success mx-2">Gerenciar Pedidos</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Gerenciar Produtos e Carrossel</h1>
        <a href="add_product.php" class="btn btn-primary">Adicionar Novo Produto</a>
        <a href="add_carousel.php" class="btn btn-secondary">Adicionar Nova Imagem ao Carrossel</a>

        <?php
        // Exibe mensagem de status
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'success') {
                echo '<div class="alert alert-success mt-3" role="alert">Ação realizada com sucesso.</div>';
            } elseif ($_GET['status'] === 'error') {
                echo '<div class="alert alert-danger mt-3" role="alert">Erro ao realizar a ação.</div>';
            }
        }
        ?>

        <h2 class="mt-5">Gerenciar Imagens do Carrossel</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrosselImagens as $imagem): ?>
                <tr>
                    <td><?php echo htmlspecialchars($imagem['id_carrossel']); ?></td>
                    <td><?php echo htmlspecialchars($imagem['nome']); ?></td>
                    <td><img src="../../assets/images/<?php echo htmlspecialchars($imagem['imagem']); ?>" alt="<?php echo htmlspecialchars($imagem['nome']); ?>" style="width: 100px;"></td>
                    <td>
                        <a href="edit_carousel.php?id=<?php echo htmlspecialchars($imagem['id_carrossel']); ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="delete_carousel.php?id=<?php echo htmlspecialchars($imagem['id_carrossel']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="mt-5">Gerenciar Produtos</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Categoria</th>
                    <th>Imagem</th>
                    <th>Tamanhos</th>
                    <th>Números de Calçados</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id_produto']); ?></td>
                    <td><?php echo htmlspecialchars($product['nome']); ?></td>
                    <td><?php echo htmlspecialchars($product['preco']); ?></td>
                    <td><?php echo htmlspecialchars($product['estoque']); ?></td>
                    <td><?php echo htmlspecialchars($product['categoria']); ?></td>
                    <td><img src="imagens/<?php echo htmlspecialchars($product['imagem']); ?>" alt="<?php echo htmlspecialchars($product['nome']); ?>" style="width: 100px;"></td>
                    <td>
                        <?php echo htmlspecialchars($product['tamanhos']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($product['numeros_calçado']); ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?php echo htmlspecialchars($product['id_produto']); ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="delete_product.php?id=<?php echo htmlspecialchars($product['id_produto']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>