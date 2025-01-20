<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Consulta informações do cliente
$sql = "SELECT * FROM cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['cliente_id']);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o cliente foi encontrado
if ($result->num_rows === 0) {
    echo "Cliente não encontrado.";
    exit();
}

$cliente = $result->fetch_assoc();
$usuarioLogado = true;

$nomeUsuario = isset($cliente['nome']) ? $cliente['nome'] : 'Usuário não encontrado';

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Painel do Cliente</title>
    <link rel="stylesheet" href="../css/user-page.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    <a href="telaprincipal.php" class="logo-link">
        <h1>Harmel</h1>
    </a>
    <div class="icons-wrapper">
        <?php if ($usuarioLogado): ?>
            <a href="adicionar_carrinho.php" class="icon-btn position-relative" onclick="verificarLoginCarrinho()">
                <i class="fas fa-shopping-cart"></i>
                <span id="carrinho-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
            </a>
        <?php else: ?>
            <a class="icon-btn" onclick="alert('Você precisa estar logado para acessar o carrinho.');"><i class="fas fa-shopping-cart"></i></a>
        <?php endif; ?>
        <a href="favoritar_produto.php" class="icon-btn position-relative">
            <i class="fas fa-heart"></i>
            <span id="favoritos-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
        </a>
        <div class="dropdown">
            <button class="botao-menu dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?php if ($usuarioLogado): ?>
                    <li class="dropdown-item user-greeting">Olá, <?= htmlspecialchars($nomeUsuario); ?></li>
                    <li><a class="dropdown-item" href="user-page.php">Meu Painel</a></li>
                    <li><a class="dropdown-item" href="configuracao_conta.php">Configurações de Conta</a></li>
                    <li><a class="dropdown-item" href="tracking.php">Rastreamento de Pedidos</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="login.php">Realizar Login</a></li>
                    <li><a class="dropdown-item" href="signup.php">Fazer Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>

<div class="container">
    <h2>Painel do Cliente</h2>
    <p>Bem-vindo, <?php echo htmlspecialchars($cliente['nome']); ?>!</p>
    <p>Email: <?php echo htmlspecialchars($cliente['email']); ?></p>

    <div class="btn-group" role="group" aria-label="Ações do Cliente">
    </div>
</div>

<div class="footer">
    <div class="footer-info">
        <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
