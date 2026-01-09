<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . url('login.php'));
    exit;
}

$user = getLoggedUser();

$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizadores");
$stats['total_users'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizadores WHERE is_admin = 1");
$stats['total_admins'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM clubes");
$stats['total_clubes'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM jogos");
$stats['total_jogos'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT u.*, c.nome as clube_favorito_nome FROM utilizadores u LEFT JOIN clubes c ON u.clube_favorito_id = c.id ORDER BY u.created_at DESC LIMIT 10");
$recent_users = $stmt->fetchAll();

$stmt = $pdo->query("SELECT c.nome, COUNT(u.id) as total_fans FROM clubes c LEFT JOIN utilizadores u ON c.id = u.clube_favorito_id GROUP BY c.id, c.nome ORDER BY total_fans DESC LIMIT 5");
$popular_clubs = $stmt->fetchAll();

$pageTitle = 'Painel de Administração';
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
    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- CSS comum -->
    <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
    <!-- CSS Admin -->
    <link rel="stylesheet" href="<?php echo url('css/admin.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Painel de Administração</h1>
                    <p>Bem-vindo, <?php echo htmlspecialchars($user['username']); ?>!</p>
                </div>
                <a href="<?php echo url('logout.php'); ?>" class="btn btn-dark">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon yellow">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Total de Utilizadores</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon red">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3><?php echo $stats['total_admins']; ?></h3>
                    <p>Administradores</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon green">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <h3><?php echo $stats['total_clubes']; ?></h3>
                    <p>Clubes Registados</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon blue">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3><?php echo $stats['total_jogos']; ?></h3>
                    <p>Jogos Registados</p>
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <h4><i class="fas fa-bolt"></i> Ações Rápidas</h4>
            <a href="<?php echo url('admin/admin-clubes.php'); ?>" class="btn btn-primary">
                <i class="fas fa-futbol"></i> Gerir Clubes
            </a>
            <a href="<?php echo url('admin/admin-jogos.php'); ?>" class="btn btn-primary">
                <i class="fas fa-calendar-alt"></i> Gerir Jogos
            </a>
            <a href="<?php echo url('admin/admin-noticias.php'); ?>" class="btn btn-primary">
                <i class="fas fa-newspaper"></i> Gerir Notícias
            </a>
            <a href="<?php echo url('admin/admin-utilizadores.php'); ?>" class="btn btn-primary">
                <i class="fas fa-users-cog"></i> Gerir Utilizadores
            </a>
            <a href="<?php echo url('index.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i> Voltar ao Site
            </a>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="table-container">
                    <h4><i class="fas fa-user-clock"></i> Utilizadores Recentes</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Utilizador</th>
                                    <th>Email</th>
                                    <th>Clube Favorito</th>
                                    <th>Tipo</th>
                                    <th>Registado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $u): ?>
                                <tr>
                                    <td>#<?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><?php echo $u['clube_favorito_nome'] ? htmlspecialchars($u['clube_favorito_nome']) : '-'; ?></td>
                                    <td>
                                        <?php if ($u['is_admin']): ?>
                                            <span class="badge-admin">ADMIN</span>
                                        <?php elseif (isset($u['is_jornalista']) && $u['is_jornalista']): ?>
                                            <span class="badge-admin" style="background-color: #3498db;">JORNALISTA</span>
                                        <?php else: ?>
                                            <span class="badge-user">USER</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="table-container">
                    <h4><i class="fas fa-star"></i> Clubes Mais Populares</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Clube</th>
                                    <th>Fãs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular_clubs as $club): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($club['nome']); ?></td>
                                    <td>
                                        <strong style="color: #f1c40f;">
                                            <?php echo $club['total_fans']; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
