<?php
session_start();

include 'db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        $sql = "SELECT * FROM cliente WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $erro = 'Email ou senha incorretos.';
        } else {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                // Define as variáveis de sessão
                $_SESSION['cliente_id'] = $usuario['id_cliente']; // Atualizado para 'cliente_id'
                $_SESSION['nome'] = $usuario['nome'];

                // Redireciona para a página principal
                header('Location: home.php');
                exit;
            } else {
                $erro = 'Email ou senha incorretos.';
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmel - Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <!-- Bootstrap CSS --> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome --> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts --> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="corpo">
    <div class="principal">
        <a href="home.php" class="logo-link me-5">
            <h1 class="logo-title">Harmel</h1>
        </a>

        <!-- Formulário de Login -->
        <div class="form-wrapper">
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="post" action="" class="form-container">
                <h2 class="form-title">Bem-vindo de volta!</h2>
                <p class="form-subtitle">Acesse sua conta para continuar</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="botao-cadastro">Entrar</button>
                    <p class="mt-3">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
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