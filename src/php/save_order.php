<?php
include 'db.php';

$produtos = $_POST['produtos'] ?? null;

if ($produtos === null || empty($produtos)) {
    echo json_encode(["success" => false, "error" => "O carrinho está vazio."]);
    exit();
}

$produtos_array = json_decode($produtos, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "error" => "Erro ao processar os produtos."]);
    exit();
}

$cliente_id = $_POST['cliente_id'];
$forma_pagamento = $_POST['forma_pagamento'];
$valor_total = $_POST['total'];
$data_pedido = date('Y-m-d H:i:s');

$conn->begin_transaction();

try {
    $sqlVenda = "INSERT INTO venda (id_cliente, total, data_compra, status) VALUES (?, ?, NOW(), 'Aguardando')";
    $stmtVenda = $conn->prepare($sqlVenda);
    $stmtVenda->bind_param("id", $cliente_id, $valor_total);
    $stmtVenda->execute();
    $id_venda = $stmtVenda->insert_id;

    foreach ($produtos_array as $produto) {
        $id_produto = $produto['id_produto'];
        $quantidade = $produto['quantidade'];

        $sql_estoque = "SELECT estoque, preco FROM produto WHERE id_produto = ?";
        $stmt = $conn->prepare($sql_estoque);
        $stmt->bind_param("i", $id_produto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['estoque'] >= $quantidade) {
                $novo_estoque = $row['estoque'] - $quantidade;
                $sql_update = "UPDATE produto SET estoque = ? WHERE id_produto = ?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("ii", $novo_estoque, $id_produto);
                $stmt->execute();

                $sqlProdutoVenda = "INSERT INTO venda_produto (id_venda, id_produto, quantidade, preco) VALUES (?, ?, ?, ?)";
                $preco = $row['preco']; // Obter o preço do produto
                $stmtProdutoVenda = $conn->prepare($sqlProdutoVenda);
                $stmtProdutoVenda->bind_param("iiid", $id_venda, $id_produto, $quantidade, $preco);
                $stmtProdutoVenda->execute();
            } else {
                throw new Exception("Estoque insuficiente para o produto: ID $id_produto");
            }
        } else {
            throw new Exception("Produto não encontrado: ID $id_produto");
        }
    }

    $conn->commit();

    header("Location: tracking.php?pedido_id=$id_venda");

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao finalizar a compra: " . $e->getMessage();
}

$conn->close();
?>