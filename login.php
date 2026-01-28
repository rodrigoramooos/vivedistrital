<?php
require_once __DIR__ . '/includes/config.php';

// Se já estiver autenticado, redirecionar
if (isLoggedIn()) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Processar login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? ''; // ?? '' define um valor padrão caso a variável não esteja definida
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && $user['password'] === $password) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id']; // quando o utilizador faz login, o ID é armazenado na sessão
                $_SESSION['username'] = $user['username']; // armazenar o nome de utilizador na sessão
                $_SESSION['is_admin'] = $user['is_admin']; // armazenar se é admin na sessão
                
                // Atualizar último login
                $updateStmt = $pdo->prepare("UPDATE utilizadores SET last_login = NOW() WHERE id = ?"); // NOW() obtém a data e hora atuais
                $updateStmt->execute([$user['id']]); // Executa a atualização do último login
                
                // Redirecionar para a página apropriada
                if ($user['is_admin']) {
                    header('Location: /vivedistrital/admin/admin.php'); // Redirecionar para o painel de administração se for admin
                } else {
                    header('Location: /vivedistrital/index.php');
                }
                exit; // Termina o script após o redirecionamento
            } else {
                $error = 'Utilizador ou palavra-passe incorretos.';
            }
        } catch (PDOException $e) {
            $error = 'Erro ao processar login. Tente novamente.';
        }
    }
}

$pageTitle = 'Login';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- CSS Comum -->
    <link rel="stylesheet" href="/vivedistrital/css/comum.css">
    <!-- CSS Autenticação -->
    <link rel="stylesheet" href="/vivedistrital/css/auth.css">
    
    <style>
        /* Estilos específicos do login */
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-link a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link a:hover {
            color: #CCCCCC;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-section">
                <img src="/vivedistrital/imgs/logos/Logo-ViveDistrital-LetrasBrancas.png" alt="Vive Distrital">
                <h2>Bem-vindo de volta!</h2>
                <p style="color: #888;">Faça login para continuar</p>
            </div>
            
            <?php if ($error): // Se houver um erro, mostrar a mensagem ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulário de login // POST é usado para enviar dados de forma segura -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Utilizador
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Digite o seu utilizador" required autofocus> <!-- autofocus foca automaticamente no campo -->
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Palavra-passe
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Digite a sua palavra-passe" required>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
            
            <div class="register-link">
                Ainda não tem conta? <a href="/vivedistrital/registo.php">Registe-se aqui</a>
            </div>
            
            <div class="back-link">
                <a href="/vivedistrital/index.php">
                    <i class="fas fa-arrow-left"></i> Voltar à página inicial
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
