<?php
header('Content-Type: application/json');
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM produto WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($produto);
        } else {
            $stmt = $pdo->query('SELECT * FROM produto');
            $produto = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($produto);
        }
        break;

    case 'POST': 
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('INSERT INTO produto (nome, descricao, preco, estoque) VALUES (?, ?, ?, ?)');
        $stmt->execute([$input['nome'], $input['descricao'], $input['preco'], $input['estoque']]);
        echo json_encode(['status' => 'Produto adicionado']);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ? WHERE id = ?');
        $stmt->execute([$input['nome'], $input['descricao'], $input['preco'], $input['estoque'], $input['id']]);
        echo json_encode(['status' => 'Produto atualizado']);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$input['id']]);
        echo json_encode(['status' => 'Produto deletado']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
?>