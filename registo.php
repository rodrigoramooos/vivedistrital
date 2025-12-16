<?php
require_once 'includes/config.php';

// Se já estiver autenticado, redirecionar
if (isLoggedIn()) {
    header('Location: ' . url('index.php'));
    exit;
}

// Obter lista de clubes para o select
$clubes = [];
try {
    $stmt = $pdo->query("SELECT * FROM clubes ORDER BY nome");
    $clubes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clubes = [];
}

// Processar registo
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $clube_favorito_id = $_POST['clube_favorito_id'] ?? null;
    
    // Validações
    if (empty($username) || empty($password) || empty($email)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (strlen($username) < 3) {
        $error = 'O utilizador deve ter pelo menos 3 caracteres.';
    } elseif (strlen($password) < 4) {
        $error = 'A palavra-passe deve ter pelo menos 4 caracteres.';
    } elseif ($password !== $confirm_password) {
        $error = 'As palavras-passe não coincidem.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um email válido.';
    } else {
        try {
            // Verificar se o utilizador já existe
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $error = 'Este nome de utilizador já está em uso.';
            } else {
                // Verificar se o email já existe
                $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $error = 'Este email já está registado.';
                } else {
                    // Criar novo utilizador
                    $stmt = $pdo->prepare("
                        INSERT INTO utilizadores (username, password, email, clube_favorito_id) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $username,
                        $password,
                        $email,
                        $clube_favorito_id ?: null
                    ]);
                    
                    $new_user_id = $pdo->lastInsertId();
                    
                    // Criar notificação de boas-vindas
                    $stmt = $pdo->prepare("
                        INSERT INTO notificacoes (utilizador_id, titulo, mensagem, tipo) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $new_user_id,
                        'Bem-vindo ao Vive Distrital!',
                        'Obrigado por se registar. Explore as funcionalidades e acompanhe o seu clube favorito!',
                        'success'
                    ]);
                    
                    $success = 'Registo realizado com sucesso! Pode agora fazer login.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Erro ao processar registo. Tente novamente.';
        }
    }
}

$pageTitle = 'Registo';
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
            padding: 2rem 0;
        }
        
        .auth-container {
            max-width: 500px;
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
        
        .form-control, .form-select {
            background-color: #2a2a2a;
            border: 1px solid #333;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #2a2a2a;
            border-color: var(--primary-accent);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
        }
        
        .form-control::placeholder {
            color: #888;
        }
        
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f1c40f' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        }
        
        .form-select option {
            background-color: #2a2a2a;
            color: white;
        }
        
        .btn-register {
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
        
        .btn-register:hover {
            background-color: #f39c12;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(241, 196, 15, 0.3);
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #CCCCCC;
        }
        
        .login-link a {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
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
        
        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-section">
                <img src="<?php echo url('imgs/logos/Logo-ViveDistrital-LetrasBrancas.png'); ?>" alt="Vive Distrital">
                <h2>Crie a sua conta</h2>
                <p style="color: #888;">Junte-se à comunidade Vive Distrital</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Utilizador <span class="required">*</span>
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Escolha um nome de utilizador" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" autofocus>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email <span class="required">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="seu@email.com" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Palavra-passe <span class="required">*</span>
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Mínimo 4 caracteres" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Confirmar palavra-passe <span class="required">*</span>
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Confirme a sua palavra-passe" required>
                </div>
                
                <div class="mb-3">
                    <label for="clube_favorito_id" class="form-label">
                        <i class="fas fa-star"></i> Clube Favorito
                    </label>
                    <select class="form-select" id="clube_favorito_id" name="clube_favorito_id">
                        <option value="">Selecione um clube (opcional)</option>
                        <?php foreach ($clubes as $clube): ?>
                            <option value="<?php echo $clube['id']; ?>"
                                <?php echo (isset($_POST['clube_favorito_id']) && $_POST['clube_favorito_id'] == $clube['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($clube['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </button>
            </form>
            
            <div class="login-link">
                Já tem conta? <a href="<?php echo url('login.php'); ?>">Faça login aqui</a>
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
