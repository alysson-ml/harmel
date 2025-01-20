<?php
session_start();
include 'db.php';

// Verifica se o usuário está logado
$usuarioLogado = isset($_SESSION['cliente_id']);
$nomeUsuario = $usuarioLogado ? htmlspecialchars($_SESSION['nome']) : '';

$baseImgPath = '../../assets/images/';

// Verifica se há uma busca
$termoBusca = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Consulta produtos
if ($termoBusca) {
    $sqlProdutos = "SELECT * FROM produto WHERE nome LIKE ? ORDER BY data_adicao DESC LIMIT 9";
    $stmt = $conn->prepare($sqlProdutos);
    $searchTerm = '%' . $termoBusca . '%';
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $resultProdutos = $stmt->get_result();
} else {
    $sqlProdutos = "SELECT * FROM produto ORDER BY data_adicao DESC LIMIT 9";
    $resultProdutos = $conn->query($sqlProdutos);
}

$produtos = [];
if ($resultProdutos && $resultProdutos->num_rows > 0) {
    while ($row = $resultProdutos->fetch_assoc()) {
        $produtos[] = $row;
    }
}

// Consulta imagens do carrossel
$sqlCarrossel = "SELECT * FROM carrossel";
$resultCarrossel = $conn->query($sqlCarrossel);
$carrosselImagens = [];

if ($resultCarrossel && $resultCarrossel->num_rows > 0) {
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
    <title>Harmel</title>
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<header class="header">
    <a href="home.php" class="logo-link">
        <h1>Harmel</h1>
    </a>
    <div class="search-wrapper">
    <input type="search" id="search-input" placeholder="Buscar Produtos..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button id="search-btn" class="search-btn"><i class="fas fa-search"></i></button>
</div>
    <div class="icons-wrapper">
        <?php if ($usuarioLogado): ?>
            <a href="cart.php" class="icon-btn position-relative" onclick="verificarLoginCarrinho()">
                <i class="fas fa-shopping-cart"></i>
                <span id="carrinho-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
            </a>
        <?php else: ?>
            <a class="icon-btn" onclick="alert('Você precisa estar logado para acessar o carrinho.');"><i class="fas fa-shopping-cart"></i></a>
        <?php endif; ?>
        <a href="favorites.php" class="icon-btn position-relative">
            <i class="fas fa-heart"></i>
            <span id="favoritos-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
        </a>
        <div class="dropdown">
            <button class="botao-menu dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?php if ($usuarioLogado): ?>
                    <li class="dropdown-item user-greeting">Olá, <?= $nomeUsuario; ?>!</li>
                    <li><a class="dropdown-item" href="user-page.php">Meu Painel</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <li><a class="dropdown-item" href="signup.php">Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>

<div class="categories">
    <a href="underwear.php">Calcinhas</a>
    <a href="maternity.php">Maternidade</a>
    <a href="body-shaper.php">Modeladores</a>
    <a href="pajamas.php">Pijamas</a>
    <a href="bras.php">Sutiãs</a>
</div>

<!-- Carrossel -->
<div class="carousel-container d-flex justify-content-between">
    <!-- Carrossel 1 -->
    <div id="carouselExampleIndicators" class="carousel slide flex-grow-1 me-2" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <?php foreach ($carrosselImagens as $index => $imagem): ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="true" aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($carrosselImagens as $index => $imagem): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="<?= $baseImgPath . htmlspecialchars($imagem['imagem']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($imagem['nome']) ?>">
                    <div class="carousel-caption d-none d-md-block">
                    <!--    <h5><?= htmlspecialchars($imagem['nome']) ?></h5> -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>


<div class="container my-4">
    <h2 class="text-center">Produtos</h2>
    <div class="row g-3">
        <?php if (count($produtos) > 0): ?>
            <?php foreach ($produtos as $produto): ?>
                <div class="col-md-4 mb-3">
                    <a href="details.php?id=<?= $produto['id_produto'] ?>" class="card-link">
                        <div class="card product-card">
                            <img src="<?= $baseImgPath . htmlspecialchars($produto['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                                <div class="price-container">
                                    <span class="product-price">R$ <?= htmlspecialchars(number_format($produto['preco'], 2, ',', '.')) ?></span>
                                </div>
                                <div class="botao-grupo">
                                    <?php if ($usuarioLogado): ?>
                                        <button onclick="adicionarAoCarrinho(<?= htmlspecialchars($produto['id_produto']) ?>, '<?= addslashes(htmlspecialchars($produto['nome'])) ?>', '<?= addslashes(htmlspecialchars($produto['imagem'])) ?>', <?= htmlspecialchars($produto['preco']) ?>, event)" class="btn btn-custom-outline d-flex align-items-center justify-content-center">
                                            <i class="fas fa-cart-plus me-2"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="favoritarProduto(<?= htmlspecialchars($produto['id_produto']) ?>, '<?= addslashes(htmlspecialchars($produto['nome'])) ?>', '<?= addslashes(htmlspecialchars($produto['imagem'])) ?>', <?= htmlspecialchars($produto['preco']) ?>, event)" class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                        <i class="fas fa-heart me-2" style="color: red;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum produto encontrado.</p>
        <?php endif; ?>
    </div>
</div>


<div class="info-section">
    <div class="info-card">
        <img src="../../assets/icons/cashback.png" alt="Harmel Club" />
        <h3>Harmel Club</h3>
        <p>Receba até 5% de cashback</p>
    </div>
    <div class="info-card">
        <img src="../../assets/icons/whatsapp.png" alt="Compre pelo WhatsApp" />
        <h3>Compre pelo WhatsApp</h3>
        <p>Fale com uma consultora Harmel</p>
    </div>
    <div class="info-card">
        <img src="../../assets/icons/pagamento-cartao.png" alt="Como comprar" />
        <h3>Como Comprar</h3>
        <p>Parcele em até 6x sem juros</p>
    </div>
</div>

<footer class="contatos">
    <h3>Entre em contato conosco:</h3>
    <div class="contatos-links">
        <a href="https://www.instagram.com/harmellingerie/"><img src="../../assets/icons/instagram.png" alt="Instagram" />@harmellingerie</a>
        <a href="https://www.facebook.com/harmeljc/?locale=pt_BR"><img src="../../assets/icons/facebook.png" alt="Facebook" />Harmel JC</a>
        <a href="#"><img src="../../assets/icons/telefone.png" alt="Telefone" />(55)99999-9999</a>
    </div>
</footer>



<div class="footer">
    <div class="footer-info">
        <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
function verificarLoginCarrinho() {
    const usuarioLogado = <?= json_encode($usuarioLogado); ?>;
    if (!usuarioLogado) {
        alert("Você precisa estar logado para acessar o carrinho.");
    } else {
        atualizarContadorCarrinho();
    }
}

function adicionarAoCarrinho(id, nome, imagem, preco, event) {
    event.preventDefault(); // Impede a navegação para detalhes.php
    let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
    
    const produtoExistente = carrinho.find(prod => prod.id === id);
    
    if (produtoExistente) {
        produtoExistente.quantidade += 1;
    } else {
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
    atualizarContadorCarrinho();
}


function atualizarContadorCarrinho() {
    let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
    let carrinhoCount = carrinho.reduce((total, produto) => total + produto.quantidade, 0);

    let carrinhoCountElement = document.getElementById('carrinho-count');
    if (carrinhoCount > 0) {
        carrinhoCountElement.innerText = carrinhoCount;
        carrinhoCountElement.style.display = 'block';
    } else {
        carrinhoCountElement.style.display = 'none';
    }
}

function favoritarProduto(id, nome, imagem, preco, event) {
    event.preventDefault(); // Impede a navegação para detalhes.php
    const usuarioLogado = <?= json_encode($usuarioLogado); ?>;

    let favoritos;
    if (usuarioLogado) {
        favoritos = JSON.parse(sessionStorage.getItem('favoritos')) || [];
    } else {
        favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    }

    // Verifica se o produto já está favoritado
    if (!favoritos.find(produto => produto.id === id)) {
        favoritos.push({ id: id, nome: nome, imagem: imagem, preco: preco });

        if (usuarioLogado) {
            sessionStorage.setItem('favoritos', JSON.stringify(favoritos)); // Salva os favoritos no sessionStorage para usuários logados
        } else {
            localStorage.setItem('favoritos', JSON.stringify(favoritos)); // Salva os favoritos no localStorage para usuários visitantes
        }

        alert(nome + ' foi adicionado aos seus favoritos!');
    } else {
        alert(nome + ' já está nos seus favoritos.');
    }

    atualizarContadorFavoritos();
}

function atualizarContadorFavoritos() {
    let favoritos;
    const usuarioLogado = <?= json_encode($usuarioLogado); ?>;

    if (usuarioLogado) {
        favoritos = JSON.parse(sessionStorage.getItem('favoritos')) || [];
    } else {
        favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    }

    const favoritosCount = favoritos.length;
    const favoritosCountElement = document.getElementById('favoritos-count');

    if (favoritosCount > 0) {
        favoritosCountElement.innerText = favoritosCount;
        favoritosCountElement.style.display = 'block';
    } else {
        favoritosCountElement.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Carrossel 1
    var carouselEl = document.querySelector('#carouselExampleIndicators');
    var carousel = new bootstrap.Carousel(carouselEl, {
        interval: 3000, // Passa a imagem automaticamente a cada 3 segundos
        pause: 'hover' // Pausa o carrossel ao passar o mouse
    });

    // Carrossel 2
    var carouselEl2 = document.querySelector('#carouselExampleIndicators2');
    var carousel2 = new bootstrap.Carousel(carouselEl2, {
        interval: 3000,
        pause: 'hover'
    });
});

document.getElementById('search-btn').addEventListener('click', function() {
    var searchQuery = document.getElementById('search-input').value;
    if (searchQuery) {
        window.location.href = "telaprincipal.php?search=" + encodeURIComponent(searchQuery);
    }
});

window.onload = function() {
    atualizarContadorFavoritos();
    atualizarContadorCarrinho();
};
</script>
</body>
</html>