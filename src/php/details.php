<?php
session_start();
include 'db.php';

$baseImgPath = '../../assets/images/';

// Verifica se o ID do produto foi passado pela URL
if (isset($_GET['id'])) {
    $idProduto = $_GET['id'];

    // Consulta as informações do produto
    $sqlProduto = "SELECT * FROM produto WHERE id_produto = ?";
    $stmt = $conn->prepare($sqlProduto);
    $stmt->bind_param('i', $idProduto);
    $stmt->execute();
    $resultProduto = $stmt->get_result();

    // Verifica se o produto existe
    if ($resultProduto->num_rows > 0) {
        $produto = $resultProduto->fetch_assoc();
    } else {
        echo "<p>Produto não encontrado.</p>";
        exit;
    }
} else {
    echo "<p>ID do produto não especificado.</p>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto - <?= htmlspecialchars($produto['nome']) ?></title>
    <link rel="stylesheet" href="details.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<header class="header">
    <a href="home.php" class="logo-link"><h1>Harmel</h1></a>
</header>

<div class="container my-4">
    <div class="card shadow-sm">
        <div class="row no-gutters">
            <div class="col-md-6">
                <img src="<?= $baseImgPath . htmlspecialchars($produto['imagem']) ?>" class="card-img" alt="<?= htmlspecialchars($produto['nome']) ?>">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h2>
                    <p class="card-text"><strong>Preço:</strong> R$ <?= htmlspecialchars(number_format($produto['preco'], 2, ',', '.')) ?></p>
                    <p class="card-text"><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria']) ?></p>
                    <p class="card-text"><strong>Tamanhos:</strong> <?= htmlspecialchars($produto['tamanhos']) ?></p>
                    <p class="card-text"><strong>Numeração:</strong> <?= htmlspecialchars($produto['numeros_calçado']) ?></p>
                    
                    <div class="d-flex justify-content-start">
                        <?php if (isset($_SESSION['cliente_id'])): // Verifica se o usuário está logado ?>
                            <button onclick="adicionarAoCarrinho(<?= htmlspecialchars($produto['id_produto']) ?>, '<?= addslashes(htmlspecialchars($produto['nome'])) ?>', '<?= addslashes(htmlspecialchars($produto['imagem'])) ?>', <?= htmlspecialchars($produto['preco']) ?>)" class="btn btn-carrinho">Adicionar ao Carrinho</button>
                        <?php else: ?>
                            <button class="adcarrinho" onclick="alert('Você precisa estar logado para adicionar ao carrinho!')">
                                <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        <?php endif; ?>

                        <button onclick="favoritarProduto(<?= htmlspecialchars($produto['id_produto']) ?>, '<?= addslashes(htmlspecialchars($produto['nome'])) ?>', '<?= addslashes(htmlspecialchars($produto['imagem'])) ?>', <?= htmlspecialchars($produto['preco']) ?>)" class="btn btn-danger">
                            <i class="fas fa-heart"></i> Favoritar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-info">
        <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    function adicionarAoCarrinho(id, nome, imagem, preco) {
        let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];

        const produtoExistente = carrinho.find(prod => prod.id === id);

        if (produtoExistente) {
            produtoExistente.quantidade += 1;
        } else {
            // Adiciona novo produto ao carrinho
            carrinho.push({
                id: id,
                nome: nome,
                imagem: imagem,
                preco: preco,
                quantidade: 1
            });
        }

        sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
        alert(nome + ' foi adicionado ao carrinho!');
    }

    function favoritarProduto(id, nome, imagem, preco) {
        let favoritos;
        const usuarioLogado = <?= json_encode(isset($_SESSION['cliente_id'])); ?>;

        if (usuarioLogado) {
            favoritos = JSON.parse(sessionStorage.getItem('favoritos')) || [];
        } else {
            favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
        }

        if (!favoritos.find(produto => produto.id === id)) {
            favoritos.push({ id: id, nome: nome, imagem: imagem, preco: preco });

            if (usuarioLogado) {
                sessionStorage.setItem('favoritos', JSON.stringify(favoritos));
            } else {
                localStorage.setItem('favoritos', JSON.stringify(favoritos));
            }

            alert(nome + ' foi adicionado aos seus favoritos!');
        } else {
            alert(nome + ' já está nos seus favoritos.');
        }
    }
</script>

</body>
</html>