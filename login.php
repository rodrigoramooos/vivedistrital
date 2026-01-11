<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . url('index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            $updateStmt = $pdo->prepare("UPDATE utilizadores SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            if ($user['is_admin']) {
                header('Location: ' . url('admin/admin.php'));
            } else {
                header('Location: ' . url('index.php'));
            }
            exit;
        } else {
            $error = 'Utilizador ou palavra-passe incorretos.';
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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/auth.css'); ?>">
    
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
