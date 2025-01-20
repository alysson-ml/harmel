<?php
session_start();

include 'db.php';

$uploadDir = '../../assets/images'; // Diretório para upload da imagem

// Cria o diretório se ele não existir
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria = $_POST['categoria'];
    $tamanho = isset($_POST['tamanho']) ? implode(',', $_POST['tamanho']) : ''; // Tamanhos selecionados
    $numeros = isset($_POST['numeros']) ? implode(',', $_POST['numeros']) : ''; // Números selecionados

    // Processa o upload da nova imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem']['name'];
        $target = $uploadDir . basename($imagem);
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target)) {
            // Imagem carregada com sucesso
            $imagem_sql = ", imagem='$imagem'";
        } else {
            $_SESSION['msg'] = "Falha ao mover o arquivo para o diretório de destino.";
            header("Location: edit_product.php?id=$id");
            exit;
        }
    } else {
        $imagem_sql = "";
    }
    
    // Atualiza o produto no banco de dados
    $sql = "UPDATE produto SET nome='$nome', preco='$preco', estoque='$estoque', categoria='$categoria', tamanhos='$tamanho', numeros_calçado='$numeros' $imagem_sql WHERE id_produto='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['msg'] = "Produto atualizado com sucesso!";
    } else {
        $_SESSION['msg'] = "Erro: " . $conn->error;
    }
    
    header("Location: edit_product.php?id=$id");
    exit;
}

// Busca produto para editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produto WHERE id_produto='$id'";
    $result = $conn->query($sql);
    
    // Verifica se o produto foi encontrado
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $tamanho_selecionado = explode(',', $product['tamanhos']);
        $numeros_selecionados = explode(',', $product['numeros_calçado']);
    } else {
        echo "Produto não encontrado.";
        exit;
    }
} else {
    echo "ID do produto não fornecido.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Editar Produto</h1>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['msg']; ?>
                <?php unset($_SESSION['msg']); // Fecha a mensagem após exibição ?>
            </div>
        <?php endif; ?>

        <form action="edit_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id_produto']); ?>">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($product['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="number" class="form-control" id="preco" name="preco" step="0.01" value="<?php echo htmlspecialchars($product['preco']); ?>" required>
            </div>
            <div class="form-group">
                <label for="estoque">Estoque</label>
                <input type="number" class="form-control" id="estoque" name="estoque" value="<?php echo htmlspecialchars($product['estoque']); ?>" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select class="form-control" id="categoria" name="categoria" required>
                    <option value="calcinhas" <?php echo ($product['categoria'] == 'calcinhas') ? 'selected' : ''; ?>>Calcinhas</option>
                    <option value="maternidade" <?php echo ($product['categoria'] == 'maternidade') ? 'selected' : ''; ?>>Maternidade</option>
                    <option value="modeladores" <?php echo ($product['categoria'] == 'modeladores') ? 'selected' : ''; ?>>Modeladores</option>
                    <option value="pijamas" <?php echo ($product['categoria'] == 'pijamas') ? 'selected' : ''; ?>>Pijamas</option>
                    <option value="sutias" <?php echo ($product['categoria'] == 'sutias') ? 'selected' : ''; ?>>Sutiãs</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tamanho">Tamanho</label><br>
                <input type="checkbox" name="tamanho[]" value="P" <?php echo (in_array('P', $tamanho_selecionado)) ? 'checked' : ''; ?>> P
                <input type="checkbox" name="tamanho[]" value="M" <?php echo (in_array('M', $tamanho_selecionado)) ? 'checked' : ''; ?>> M
                <input type="checkbox" name="tamanho[]" value="G" <?php echo (in_array('G', $tamanho_selecionado)) ? 'checked' : ''; ?>> G
                <input type="checkbox" name="tamanho[]" value="GG" <?php echo (in_array('GG', $tamanho_selecionado)) ? 'checked' : ''; ?>> GG
            </div>

            <div class="form-group">
                <label>Números de Calçado Disponíveis</label>
                <div>
                    <input type="checkbox" id="numero_35_36" name="numeros[]" value="35-36" <?php echo (in_array('35-36', $numeros_selecionados)) ? 'checked' : ''; ?>>
                    <label for="numero_35_36">35-36</label>
                </div>
                <div>
                    <input type="checkbox" id="numero_37_38" name="numeros[]" value="37-38" <?php echo (in_array('37-38', $numeros_selecionados)) ? 'checked' : ''; ?>>
                    <label for="numero_37_38">37-38</label>
                </div>
                <div>
                    <input type="checkbox" id="numero_39_40" name="numeros[]" value="39-40" <?php echo (in_array('39-40', $numeros_selecionados)) ? 'checked' : ''; ?>>
                    <label for="numero_39_40">39-40</label>
                </div>
                <div>
                    <input type="checkbox" id="numero_41_42" name="numeros[]" value="41-42" <?php echo (in_array('41-42', $numeros_selecionados)) ? 'checked' : ''; ?>>
                    <label for="numero_41_42">41-42</label>
                </div>
            </div>

            <div class="form-group">
                <label for="imagem">Imagem</label>
                <input type="file" class="form-control-file" id="imagem" name="imagem">
                <?php if ($product['imagem']): ?>
                    <p>Imagem atual: <img src="<?php echo htmlspecialchars($uploadDir . $product['imagem']); ?>" alt="Imagem do produto" width="100"></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="admin.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tamanhoCheckboxes = document.querySelectorAll('input[name="tamanho[]"]');
        const numeroCheckboxes = document.querySelectorAll('input[name="numeros[]"]');

        function toggleCheckboxes(checkboxes, isEnabled) {
            checkboxes.forEach(checkbox => {
                checkbox.disabled = !isEnabled;
                if (!isEnabled) {
                    checkbox.checked = false;
                }
            });
        }

        tamanhoCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const hasSelected = Array.from(tamanhoCheckboxes).some(cb => cb.checked);
                toggleCheckboxes(numeroCheckboxes, !hasSelected);
            });
        });

        numeroCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const hasSelected = Array.from(numeroCheckboxes).some(cb => cb.checked);
                toggleCheckboxes(tamanhoCheckboxes, !hasSelected);
            });
        });
    });
</script>

</body>
</html>