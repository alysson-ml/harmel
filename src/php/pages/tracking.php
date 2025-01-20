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