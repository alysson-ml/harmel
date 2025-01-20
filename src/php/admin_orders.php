<?php
session_start();
include('db.php');

$query = "SELECT * FROM venda";
$result = $conn->query($query);
$pedidos = $result->fetch_all(MYSQLI_ASSOC);

// Atualizando o status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $novo_status = $_POST['status'] ?? null;

    if ($pedido_id && $novo_status) {
        $updateQuery = "UPDATE venda SET status = ? WHERE id_venda = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $novo_status, $pedido_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: admin_orders.php?status=success");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administração de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1>Administração de Pedidos</h1>
    <a href="admin.php" class="btn btn-secondary mb-3">Voltar à Página Principal</a>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success" role="alert">
            Pedido atualizado com sucesso!
        </div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID do Pedido</th>
                <th>Nome do Cliente</th>
                <th>Status</th>
                <th>Data</th>
                <th>Forma de Pagamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pedido['id_venda']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['status']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['data_compra']); ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($pedido['id_venda']); ?>">
                            <select name="status" class="form-select">
                                <option value="Aguardando" <?php echo $pedido['status'] === 'Aguardando' ? 'selected' : ''; ?>>Aguardando</option>
                                <option value="Processado" <?php echo $pedido['status'] === 'Processado' ? 'selected' : ''; ?>>Processado</option>
                                <option value="Em trânsito" <?php echo $pedido['status'] === 'Em trânsito' ? 'selected' : ''; ?>>Em trânsito</option>
                                <option value="Entregue" <?php echo $pedido['status'] === 'Entregue' ? 'selected' : ''; ?>>Entregue</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>