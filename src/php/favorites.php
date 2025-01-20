<?php
session_start();
$usuarioLogado = isset($_SESSION['cliente_id']);
$usuarioId = $usuarioLogado ? $_SESSION['cliente_id'] : 'guest';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Meus Favoritos</title>
    <link rel="stylesheet" href="../css/favorites.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<header class="header">
<a href="home.php" class="logo-link">
        <h1>Harmel</h1>
    </a>
</header>
<body>
<div class="container my-4">
    <h2 class="text-center">Meus Favoritos</h2>
    <div id="favoritos-list" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    </div>
</div>

<div class="footer">
    <div class="footer-info">
        <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</div>

<script>
    function carregarFavoritos() {
    const usuarioId = <?= json_encode($usuarioId); ?>;
    let favoritos = JSON.parse(localStorage.getItem(`favoritos_${usuarioId}`)) || [];

    console.log('Favoritos carregados:', favoritos);

    const lista = document.getElementById('favoritos-list');
    lista.innerHTML = '';

    if (favoritos.length > 0) {
        favoritos.forEach(produto => {
            console.log('Produto:', produto);
            const item = document.createElement('div');
            item.className = 'col mb-3';
            item.innerHTML = `
                <div class="card">
                    <img src="imagens/${produto.imagem || 'default.jpg'}" class="card-img-top" alt="${produto.nome}" onerror="this.onerror=null; this.src='../../assets/images/default.jpg';">
                    <div class="card-body">
                        <h5 class="card-title">${produto.nome}</h5>
                        <p class="card-text">R$ ${produto.preco.toFixed(2).replace('.', ',')}</p>
                        <button onclick="removerFavorito(${produto.id})" class="btn btn-outline-danger">Remover dos Favoritos</button>
                    </div>
                </div>
            `;
            lista.appendChild(item);
        });
    } else {
        lista.innerHTML = '<p>Nenhum produto favoritado.</p>';
    }
}

    function carregarFavoritos() {
    const usuarioLogado = <?= json_encode($usuarioLogado); ?>;
    let favoritos;

    // Verifica se o usuário está logado
    if (usuarioLogado) {
        favoritos = JSON.parse(sessionStorage.getItem('favoritos')) || [];
    } else {
        favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    }

    console.log('Favoritos carregados:', favoritos);

    const lista = document.getElementById('favoritos-list');
    lista.innerHTML = '';

    if (favoritos.length > 0) {
        favoritos.forEach(produto => {
            console.log('Produto:', produto);
            const item = document.createElement('div');
            item.className = 'col-md-4 mb-3';
            item.innerHTML = `
                <div class="card">
                    <img src="../../assets/images/${produto.imagem || 'default.jpg'}" class="card-img-top" alt="${produto.nome}" onerror="this.onerror=null; this.src='imagens/default.jpg';">
                    <div class="card-body">
                        <h5 class="card-title">${produto.nome}</h5>
                        <p class="card-text">R$ ${produto.preco.toFixed(2).replace('.', ',')}</p>
                        <button onclick="removerFavorito(${produto.id})" class="btn btn-outline-danger">Remover dos Favoritos</button>
                    </div>
                </div>
            `;
            lista.appendChild(item);
        });
    } else {
        lista.innerHTML = '<p>Nenhum produto favoritado.</p>';
    }
}

    // Função para remover um produto dos favoritos
    function removerFavorito(id) {
    const usuarioLogado = <?= json_encode($usuarioLogado); ?>;
    let favoritos;

    // Verifica se o usuário está logado
    if (usuarioLogado) {
        favoritos = JSON.parse(sessionStorage.getItem('favoritos')) || [];
    } else {
        favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    }

    // Filtra o produto removendo o item com o ID fornecido
    favoritos = favoritos.filter(produto => produto.id !== id);

    // Atualiza o armazenamento (sessionStorage ou localStorage)
    if (usuarioLogado) {
        sessionStorage.setItem('favoritos', JSON.stringify(favoritos));
    } else {
        localStorage.setItem('favoritos', JSON.stringify(favoritos));
    }

    carregarFavoritos(); // Recarrega os favoritos após a remoção
}

    window.onload = carregarFavoritos;
</script>
</body>
</html>