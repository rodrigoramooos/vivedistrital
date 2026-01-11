<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . url('index.php'));
    exit;
}

$clubes = [];
$stmt = $pdo->query("SELECT * FROM clubes ORDER BY nome");
$clubes = $stmt->fetchAll();

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $clube_favorito_id = $_POST['clube_favorito_id'] ?? null;
    
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
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = 'Este nome de utilizador já está em uso.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Este email já está registado.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO utilizadores (username, password, email, clube_favorito_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $password, $email, $clube_favorito_id ?: null]);
                
                $new_user_id = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO notificacoes (utilizador_id, titulo, mensagem, tipo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$new_user_id, 'Bem-vindo ao Vive Distrital!', 'Obrigado por se registar. Explore as funcionalidades e acompanhe o seu clube favorito!', 'success']);
                
                $success = 'Registo realizado com sucesso! Pode agora fazer login.';
            }
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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/auth.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/registo.css'); ?>">
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
