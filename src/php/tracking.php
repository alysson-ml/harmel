<?php
session_start();
include 'db.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    echo "Você precisa estar logado para acessar o rastreamento de pedidos.";
    exit;
}

$clienteId = $_SESSION['cliente_id'];

// Consulta os pedidos do cliente, incluindo a forma de pagamento
$sqlPedidos = "SELECT id_venda, status, data_compra, total, produtos, forma_pagamento FROM venda WHERE id_cliente = ? ORDER BY data_compra DESC";
$stmtPedidos = $conn->prepare($sqlPedidos);
$stmtPedidos->bind_param("i", $clienteId);
$stmtPedidos->execute();
$resultPedidos = $stmtPedidos->get_result();

$pedidos = $resultPedidos->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Rastreio de Pedidos</title>
    <link rel="stylesheet" href="../css/tracking.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <a href="telaprincipal.php" class="logo-link">
            <h1>Harmel</h1>
        </a>
        <div class="icons-wrapper">
            <!-- Botão para retornar ao painel -->
            <a href="tela_usuario.php" class="btn btn-primary">Voltar ao Painel</a>
        </div>
    </header>

    <div class="container my-4">
        <h1>Rastreio de Pedidos</h1>

        <?php if (!empty($pedidos)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID do Pedido</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Produtos</th>
                        <th>Forma de Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['id_venda']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['status']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['data_compra']); ?></td>
                            <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                // Decodifica o JSON com os produtos
                                $produtos = json_decode($pedido['produtos'], true);

                                if (is_array($produtos) && !empty($produtos)): ?>
                                    <ul>
                                        <?php foreach ($produtos as $produto): ?>
                                            <li>
                                                <?php echo htmlspecialchars($produto['nome']); ?> 
                                                (Qtd: <?php echo htmlspecialchars($produto['quantidade']); ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    Nenhum produto encontrado para este pedido ou erro na decodificação do JSON.
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['forma_pagamento']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Nenhum pedido encontrado.
            </div>
        <?php endif; ?>

        <!-- Legenda de Status -->
        <h5>Legenda de Status dos Pedidos</h5>
        <ul>
            <li><strong>Aguardando:</strong> Aguardando pagamento</li>
            <li><strong>Processando:</strong> Pedido sendo processado</li>
            <li><strong>Enviado:</strong> Pedido enviado</li>
            <li><strong>Entregue:</strong> Pedido entregue</li>
            <li><strong>Cancelado:</strong> Pedido cancelado</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-info">
            <p>© Harmel. 2024. Todos os direitos reservados.</p>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>