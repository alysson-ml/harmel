<?php
include 'db.php';

$uploadDir = '../../assets/images/'; // Diretório para upload da imagem

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria = $_POST['categoria'];

    // Validação para tamanhos e números de calçado
    if (!empty($_POST['tamanhos']) && !empty($_POST['numeros_calçado'])) {
        echo "Erro: Não é permitido selecionar tamanhos e números de calçado ao mesmo tempo.";
        exit;
    }

    $tamanhos = !empty($_POST['tamanhos']) ? implode(", ", $_POST['tamanhos']) : null;
    $numeros_calçado = !empty($_POST['numeros_calçado']) ? implode(", ", $_POST['numeros_calçado']) : null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem']['name'];
        $target = $uploadDir . basename($imagem);

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target)) {
        } else {
            echo "Falha ao mover o arquivo para o diretório de destino.";
            $imagem = '';
        }
    } else {
        $imagem = '';
    }

    $stmt = $conn->prepare("INSERT INTO produto (nome, preco, estoque, categoria, imagem, tamanhos, numeros_calçado, data_adicao) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sdsssss", $nome, $preco, $estoque, $categoria, $imagem, $tamanhos, $numeros_calçado);

    if ($stmt->execute()) {
        echo "Novo produto adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar o produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Novo Produto</h1>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="number" class="form-control" id="preco" name="preco" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="estoque">Estoque</label>
                <input type="number" class="form-control" id="estoque" name="estoque" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select class="form-control" id="categoria" name="categoria" required>
                    <option value="calcinhas">Calcinhas</option>
                    <option value="maternidade">Maternidade</option>
                    <option value="modeladores">Modeladores</option>
                    <option value="pijamas">Pijamas</option>
                    <option value="sutias">Sutiãs</option>
                </select>
            </div>

<div class="form-group">
    <label>Tamanhos Disponíveis</label>
    <div>
        <input type="checkbox" id="tamanho_p" name="tamanhos[]" value="P">
        <label for="tamanho_p">P</label>
    </div>
    <div>
        <input type="checkbox" id="tamanho_m" name="tamanhos[]" value="M">
        <label for="tamanho_m">M</label>
    </div>
    <div>
        <input type="checkbox" id="tamanho_g" name="tamanhos[]" value="G">
        <label for="tamanho_g">G</label>
    </div>
    <div>
        <input type="checkbox" id="tamanho_gg" name="tamanhos[]" value="GG">
        <label for="tamanho_gg">GG</label>
    </div>
</div>

<div class="form-group">
    <label>Números de Calçado Disponíveis</label>
    <div>
        <input type="checkbox" id="numero_35_36" name="numeros_calçado[]" value="35-36">
        <label for="numero_35_36">35-36</label>
    </div>
    <div>
        <input type="checkbox" id="numero_37_38" name="numeros_calçado[]" value="37-38">
        <label for="numero_37_38">37-38</label>
    </div>
    <div>
        <input type="checkbox" id="numero_39_40" name="numeros_calçado[]" value="39-40">
        <label for="numero_39_40">39-40</label>
    </div>
    <div>
        <input type="checkbox" id="numero_41_42" name="numeros_calçado[]" value="41-42">
        <label for="numero_41_42">41-42</label>
    </div>
</div>

            <div class="form-group">
                <label for="imagem">Imagem</label>
                <input type="file" class="form-control-file" id="imagem" name="imagem">
            </div>
            <button type="submit" class="btn btn-primary">Adicionar Produto</button>
            <a href="admin.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>

<script>
    const tamanhos = document.querySelectorAll("input[name='tamanhos[]']");
    const numerosCalçado = document.querySelectorAll("input[name='numeros_calçado[]']");

    function toggleOptions(disableGroup, enableGroup) {
        disableGroup.forEach(input => input.disabled = true);
        enableGroup.forEach(input => input.disabled = false);
    }

    function checkAndReactivate() {
        const isTamanhosChecked = Array.from(tamanhos).some(input => input.checked);
        const isNumerosChecked = Array.from(numerosCalçado).some(input => input.checked);

        if (!isTamanhosChecked) {
            numerosCalçado.forEach(input => input.disabled = false);
        }
        if (!isNumerosChecked) {
            tamanhos.forEach(input => input.disabled = false);
        }
    }

    // Listener para tamanhos
    tamanhos.forEach(input => {
        input.addEventListener("change", () => {
            if (Array.from(tamanhos).some(input => input.checked)) {
                toggleOptions(numerosCalçado, tamanhos);
            } else {
                checkAndReactivate();
            }
        });
    });

    // Listener para números de calçado
    numerosCalçado.forEach(input => {
        input.addEventListener("change", () => {
            if (Array.from(numerosCalçado).some(input => input.checked)) {
                toggleOptions(tamanhos, numerosCalçado);
            } else {
                checkAndReactivate();
            }
        });
    });
</script>
</body>
</html>