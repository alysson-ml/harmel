<?php
session_start();

// Redireciona para a tela principal caso o usuário já esteja logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: home.php');
    exit;
}

include 'db.php';

// Inicializa variáveis
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém e sanitiza os dados do formulário
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $data_nasc = trim($_POST['data_nasc']);

    // Valida dados
    if (empty($nome) || empty($sobrenome) || empty($email) || empty($senha) || empty($endereco) || empty($telefone) || empty($data_nasc)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        // Verifica se o email já está registrado
        $sql = "SELECT * FROM cliente WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $erro = 'O email já está registrado.';
        } else {
            // Registra novo cliente
            $sql = "INSERT INTO cliente (nome, sobrenome, email, senha, endereco, telefone, data_nasc) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            // Criptografa a senha antes de armazenar
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt->bind_param('sssssss', $nome, $sobrenome, $email, $senha_hash, $endereco, $telefone, $data_nasc);
            if ($stmt->execute()) {
                // Inicia sessão e define variáveis
                $_SESSION['email'] = $email;
                $_SESSION['nome'] = $nome;
                $_SESSION['sobrenome'] = $sobrenome;
                
                // Redireciona para a tela principal
                header('Location: home.php');
                exit;
            } else {
                $erro = 'Ocorreu um erro. Tente novamente.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Cadastro</title>
    <link rel="stylesheet" href="../css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="corpo">
    <div class="principal">
    <a href="home.php" class="logo-link me-5">
        <h1 class="logo-title">Harmel</h1>
    </a>

        <div class="form-wrapper">
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="post" action="" class="form-container">
                <h2 class="form-title">Criar uma conta!</h2>
                <p class="form-subtitle">Compre mais rápido e acompanhe seus pedidos em um só lugar</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="sobrenome" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control" id="sobrenome" name="sobrenome" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="data_nasc" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nasc" name="data_nasc" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="botao-cadastro">Criar Conta</button>
                    <p class="mt-3">Já possui uma conta? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-info">
        <p>© Harmel. 2024. Todos os direitos reservados.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>