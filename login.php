<?php
require_once 'includes/config.php';

// Se já estiver autenticado, redirecionar
if (isLoggedIn()) {
    header('Location: ' . url('index.php'));
    exit;
}

// Processar login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // Atualizar último login
                $updateStmt = $pdo->prepare("UPDATE utilizadores SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Redirecionar para a página apropriada
                if ($user['is_admin']) {
                    header('Location: ' . url('admin.php'));
                } else {
                    header('Location: ' . url('index.php'));
                }
                exit;
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
    
    <style>
        :root {
            --bs-body-bg: #0D0D0D;
            --bs-body-color: #FFFFFF;
            --primary-accent: #f1c40f;
        }
        
        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
        }
        
        .auth-card {
            background-color: #1A1A1A;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-section img {
            max-width: 200px;
            margin-bottom: 1rem;
        }
        
        .logo-section h2 {
            color: var(--primary-accent);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .form-label {
            color: #CCCCCC;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            background-color: #2a2a2a;
            border: 1px solid #333;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
        }
        
        .form-control:focus {
            background-color: #2a2a2a;
            border-color: var(--primary-accent);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
        }
        
        .form-control::placeholder {
            color: #888;
        }
        
        .btn-login {
            width: 100%;
            background-color: var(--primary-accent);
            border: none;
            color: #000;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 8px;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #f39c12;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(241, 196, 15, 0.3);
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #CCCCCC;
        }
        
        .register-link a {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
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
                <img src="<?php echo url('imgs/logos/Logo-ViveDistrital-LetrasBrancas.png'); ?>" alt="Vive Distrital">
                <h2>Bem-vindo de volta!</h2>
                <p style="color: #888;">Faça login para continuar</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Utilizador
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Digite o seu utilizador" required autofocus>
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
                Ainda não tem conta? <a href="<?php echo url('registo.php'); ?>">Registe-se aqui</a>
            </div>
            
            <div class="back-link">
                <a href="<?php echo url('index.php'); ?>">
                    <i class="fas fa-arrow-left"></i> Voltar à página inicial
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
