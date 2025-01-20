<?php
session_start();

if (!isset($_SESSION['cliente_id'])) {
    echo "Você precisa estar logado para acessar o carrinho!";
    exit;
}

// Inicializa o carrinho para o cliente logado
$clienteId = $_SESSION['cliente_id'];
if (!isset($_SESSION['carrinho'][$clienteId])) {
    $_SESSION['carrinho'][$clienteId] = [];
}

function adicionarAoCarrinho($produto) {
    global $clienteId;
    $idProduto = $produto['id'];
    $existe = false;

    foreach ($_SESSION['carrinho'][$clienteId] as $key => $item) {
        if ($item['id'] == $idProduto) {
            $_SESSION['carrinho'][$clienteId][$key]['quantidade'] += $produto['quantidade'];
            $existe = true;
            break;
        }
    }

    if (!$existe) {
        $_SESSION['carrinho'][$clienteId][] = $produto;
    }
}

function alterarQuantidadeCarrinho($idProduto, $quantidade) {
    global $clienteId;
    foreach ($_SESSION['carrinho'][$clienteId] as $key => $item) {
        if ($item['id'] == $idProduto) {
            if ($quantidade > 0) {
                $_SESSION['carrinho'][$clienteId][$key]['quantidade'] = $quantidade;
            } else {
                unset($_SESSION['carrinho'][$clienteId][$key]);
            }
            break;
        }
    }
}

function removerProdutoCarrinho($idProduto) {
    global $clienteId;
    foreach ($_SESSION['carrinho'][$clienteId] as $key => $item) {
        if ($item['id'] == $idProduto) {
            unset($_SESSION['carrinho'][$clienteId][$key]);
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? null;
    $produto = $data['produto'] ?? null;

    if ($action === 'adicionar' && $produto) {
        adicionarAoCarrinho($produto);
    } elseif ($action === 'alterarQuantidade' && isset($produto['id'], $produto['quantidade'])) {
        alterarQuantidadeCarrinho($produto['id'], $produto['quantidade']);
    } elseif ($action === 'remover' && isset($produto['id'])) {
        removerProdutoCarrinho($produto['id']);
    }

    echo json_encode(['carrinho' => $_SESSION['carrinho'][$clienteId]]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Carrinho de Compras</title>
    <link rel="stylesheet" href="../css/cart.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AeQ4KBM2b_ItReGoPt3kcwh9luH425yf_Hi-9Hz5Ui8HuTNrEQXj34SoibRKkBjTVcogJzFjuZyh5XiG&currency=BRL"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div>
<header class="header">
        <a href="home.php" class="logo-link">
            <h1>Harmel</h1>
        </a>
        <div class="dropdown">
            <button class="botao-menu dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i> 
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?= htmlspecialchars($_SESSION['nome']); ?>
                <?php else: ?>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="dropdown-item user-greeting">Olá, <?= htmlspecialchars($_SESSION['nome']); ?></li>
                    <li><a class="dropdown-item" href="user-page.php">Meu Painel</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <li><a class="dropdown-item" href="signup.php">Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </div>
</header>

<style>
        .payment-button {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            padding: 10px 20px;
            gap: 8px;
            border: 2px solid #4b0082;
            background-color: #fff;
            color: #000;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .payment-button:hover {
            background-color: #4b0082;
            color: #fff;
            border: 2px solid #4b0082;
        }
        .payment-button i {
            font-size: 20px;
        }
    </style>

<div class="container my-4">
    <h2 class="text-center mb-4">Produtos no Carrinho</h2>
    <div class="row text-center">
        <div class="col-3 descricao-ajustada">
            <p><strong>Descrição</strong></p>
        </div>
        <div class="col-2 quantidade-ajustada">
            <p><strong>Quantidade</strong></p>
        </div>
        <div class="col-2 remover-ajustada">
            <p><strong>Remover</strong></p>
        </div>
        <div class="col-3 preco-ajustada">
            <p><strong>Preço</strong></p>
        </div>
    </div>

    <div class="row" id="produtos">
    <div class="col-12 col-md-6 col-lg-4 mb-3" id="produto-1">
        <div class="produto d-flex justify-content-between align-items-center">
            <div class="produto-descricao col-3">
                <p>Produto Descrição Aqui</p>
            </div>

            <div class="produto-quantidade col-2 text-center">
                <p><strong>Quantidade</strong></p>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-secondary btn-sm me-2">-</button>
                    <span class="quantidade-text mx-2">1</span>
                    <button class="btn btn-secondary btn-sm ms-2">+</button>
                </div>
            </div>

            <div class="produto-remover col-2 text-center">
                <button class="btn btn-danger btn-sm">Remover</button>
            </div>

            <div class="produto-preco col-3 text-center">
                <p><strong>R$ 100,00</strong></p>
            </div>
        </div>
    </div>
</div>


<div class="text-end my-3">
        <h4> <span id="totalCarrinho">R$ 0.00</span></h4>
        <div id="paypal-button-container" class="text-center my-4"></div>

        <button class="btn btn-success payment-button" onclick="realizarCompra('PayPal')">
            <i class="bi bi-paypal"></i> PayPal
        </button>
        <button class="btn btn-primary payment-button" onclick="realizarCompra('Pix')">
            <i class="bi bi-qr-code"></i> Pix
        </button>
        <button class="btn btn-warning payment-button" onclick="realizarCompra('Boleto')">
            <i class="bi bi-receipt-cutoff"></i> Boleto
        </button>
        <button class="btn btn-info payment-button" onclick="realizarCompra('CartaoCredito')">
            <i class="bi bi-credit-card"></i> Cartão de Crédito
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<footer class="footer">
    <div class="footer-info">
    <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
    function adicionarAoCarrinho(produtoId, nome, preco, quantidade, imagem, tamanhos, numerosCalçado) {
        const produto = {
            id: produtoId,
            nome: nome,
            preco: preco,
            quantidade: quantidade,
            imagem: imagem,
            tamanhos: tamanhos || null,
            numeros_calçado: numerosCalçado || null
        };

        fetch('carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'adicionar', produto: produto })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Produto adicionado ao carrinho:', data);
        })
        .catch(error => console.error('Erro ao adicionar ao carrinho:', error));
    }

        function calcularTotal() {
            let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
            return carrinho.reduce((acc, prod) => acc + (parseFloat(prod.preco) * prod.quantidade), 0);
        }

        function exibirTotal() {
            const total = calcularTotal().toFixed(2);
            const totalCarrinhoDiv = document.getElementById('totalCarrinho');
            totalCarrinhoDiv.innerHTML = `Total: R$ ${total}`;
        }

        // Atualiza o total sempre que o carrinho for alterado
        function carregarCarrinho() {
            let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
            const produtosDiv = document.getElementById('produtos');
            produtosDiv.innerHTML = ''; // Limpa o conteúdo anterior

    if (carrinho.length > 0) {
        carrinho.forEach(produto => {
            const imagem = produto.imagem || 'default.jpg';
            const nome = produto.nome || 'Produto desconhecido';
            const preco = produto.preco || '0.00';
            const quantidade = produto.quantidade || 1;

            const produtoHTML = `
                <div class="produto d-flex align-items-center mb-3" id="produto-${produto.id}">
                    <div class="produto-imagem me-3">
                        <img src="../../assets/images/${imagem}" alt="${nome}" onerror="this.onerror=null; this.src='../../assets/images/default.jpg';" style="width: 100px; height: 100px; object-fit: cover;">
                        <span class="produto-nome">${nome}</span> <!-- Adicionando a classe 'produto-nome' -->
                    </div>
                    <div class="produto-quantidade d-flex align-items-center me-3">
                        <button class="btn btn-secondary btn-sm me-2" onclick="alterarQuantidade(${produto.id}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantidade-text mx-2" id="quantidade-${produto.id}">${quantidade}</span>
                        <button class="btn btn-secondary btn-sm ms-2" onclick="alterarQuantidade(${produto.id}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="produto-remover">
                        <button class="btn btn-danger btn-sm d-flex justify-content-center align-items-center" onclick="removerProduto(${produto.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="produto-info flex-grow-1">
                        <h5 class="mb-1 d-flex justify-content-between">
                        <span class="preco-produto text-center">R$ ${parseFloat(preco).toFixed(2)}</span>
                    </div>
                </div>
            `;
            produtosDiv.innerHTML += produtoHTML;
        });
    } else {
        produtosDiv.innerHTML = '<p>Seu carrinho está vazio.</p>';
    }

    exibirTotal();
}

        // Atualiza o total após qualquer alteração no carrinho (adicionar, remover, alterar quantidade)
        function alterarQuantidade(id, operacao) {
            let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
            const produto = carrinho.find(p => p.id === id);
            
            if (produto) {
                if (operacao === 1) {
                    produto.quantidade += 1;
                } else if (operacao === -1 && produto.quantidade > 1) {
                    produto.quantidade -= 1;
                }

                sessionStorage.setItem('carrinho', JSON.stringify(carrinho)); // Atualiza o carrinho no sessionStorage
                carregarCarrinho();
            }
        }

        function removerProduto(id) {
            let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
            carrinho = carrinho.filter(produto => produto.id !== id);
            sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
            carregarCarrinho(); // Recarrega o carrinho para refletir a remoção
        }

function calcularTotal() {
    let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || [];
    return carrinho.reduce((acc, prod) => acc + (parseFloat(prod.preco) * prod.quantidade), 0);
}

function perguntarPermissao() {
    return new Promise((resolve, reject) => {
        // Simulação de um prompt para o usuário
        const permissao = confirm("Você tem certeza que deseja realizar a compra?");
        if (permissao) {
            resolve();
        } else {
            reject("Compra cancelada pelo usuário.");
        }
    });
}

function realizarCompra(metodoPagamento) {
    // Recupera o carrinho do sessionStorage
    let cliente = JSON.parse(sessionStorage.getItem('cliente'));
    let carrinho = JSON.parse(sessionStorage.getItem("carrinho"));

    if (!carrinho || carrinho.length === 0) {
        alert("O carrinho está vazio.");
        return;
    }

    perguntarPermissao()
        .then(() => {
            // Simulação de compra
            alert("Compra realizada com sucesso via " + metodoPagamento);

            sessionStorage.removeItem("carrinho");

            window.location.href = "tracking.php";
        })
        .catch((erro) => {
            alert(erro);
        });
}

window.onload = carregarCarrinho;
</script>
</body>
</html>