<?php
session_start();

$carrinho = json_decode(file_get_contents('php://input'), true);

if (isset($carrinho)) {
    $_SESSION['carrinho'] = json_encode($carrinho);
}
?>