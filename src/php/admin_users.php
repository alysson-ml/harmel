<?php
session_start();
include('db.php');

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$query = "SELECT * FROM cliente WHERE id_cliente IS NOT NULL";
if ($filter) {
    $query .= " AND (nome LIKE ? OR sobrenome LIKE ?)";
}
$stmt = $conn->prepare($query);

if ($filter) {
    $filterParam = '%' . $filter . '%';
    $stmt->bind_param("ss", $filterParam, $filterParam);
} else {
    $stmt->execute();
}

$stmt->execute();
$result = $stmt->get_result();
$clientes = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $cliente_id = $_POST['cliente_id'] ?? null;
    
    if ($cliente_id) {
        $deleteQuery = "DELETE FROM cliente WHERE id_cliente = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: admin_users.php?status=deleted");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Administração de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <a href="admin.php" class="btn btn-secondary mb-3">Voltar para a Tela Principal</a>

    <h1>Administração de Clientes</h1>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert alert-success" role="alert">
                Cliente atualizado com sucesso!
            </div>
        <?php elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-danger" role="alert">
                Cliente excluído com sucesso!
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="filter" placeholder="Filtrar por nome ou sobrenome" value="<?php echo htmlspecialchars($filter); ?>">
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID do Cliente</th>
                <th>Nome</th>
                <th>Sobrenome</th>
                <th>Email</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Data de Nascimento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cliente['id_cliente']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['sobrenome']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['endereco']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['data_nasc']); ?></td>
                    <td>

                        <form method="POST" class="d-inline">
                            <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($cliente['id_cliente']); ?>">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $cliente['id_cliente']; ?>">
                                Excluir
                            </button>

                            <div class="modal fade" id="confirmDeleteModal<?php echo $cliente['id_cliente']; ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir a conta deste cliente?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" name="delete" class="btn btn-danger">Excluir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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