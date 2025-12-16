<?php
require_once 'includes/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . url('login.php'));
    exit;
}

$user = getLoggedUser();

// Obter estatísticas
$stats = [];
try {
    // Total de utilizadores
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizadores");
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Total de admins
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizadores WHERE is_admin = 1");
    $stats['total_admins'] = $stmt->fetch()['total'];
    
    // Total de clubes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubes");
    $stats['total_clubes'] = $stmt->fetch()['total'];
    
    // Total de jogos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM jogos");
    $stats['total_jogos'] = $stmt->fetch()['total'];
    
    // Utilizadores recentes
    $stmt = $pdo->query("
        SELECT u.*, c.nome as clube_favorito_nome 
        FROM utilizadores u 
        LEFT JOIN clubes c ON u.clube_favorito_id = c.id 
        ORDER BY u.created_at DESC 
        LIMIT 10
    ");
    $recent_users = $stmt->fetchAll();
    
    // Clubes mais populares
    $stmt = $pdo->query("
        SELECT c.nome, COUNT(u.id) as total_fans 
        FROM clubes c 
        LEFT JOIN utilizadores u ON c.id = u.clube_favorito_id 
        GROUP BY c.id, c.nome 
        ORDER BY total_fans DESC 
        LIMIT 5
    ");
    $popular_clubs = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Erro ao carregar estatísticas.';
}

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
    
    <style>
        .admin-header {
            background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
            color: #000;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(241, 196, 15, 0.3);
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .admin-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.8;
        }
        
        .stat-card {
            background-color: #1A1A1A;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #333;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .stat-card .icon.yellow {
            background-color: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }
        
        .stat-card .icon.blue {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        
        .stat-card .icon.green {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
        
        .stat-card .icon.red {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #f1c40f;
            margin: 0;
        }
        
        .stat-card p {
            color: #888;
            margin: 0.5rem 0 0 0;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .table-container {
            background-color: #1A1A1A;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #333;
        }
        
        .table-container h4 {
            color: #f1c40f;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }
        
        .table {
            color: white;
        }
        
        .table thead {
            border-bottom: 2px solid #333;
        }
        
        .table th {
            color: #f1c40f;
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        
        .table td {
            padding: 1rem;
            border-color: #2a2a2a;
        }
        
        .table tbody tr:hover {
            background-color: #222;
        }
        
        .badge-admin {
            background-color: #e74c3c;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-user {
            background-color: #3498db;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .quick-actions {
            background-color: #1A1A1A;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #333;
        }
        
        .quick-actions h4 {
            color: #f1c40f;
            margin-bottom: 1rem;
        }
        
        .quick-actions .btn {
            margin: 0.5rem 0.5rem 0.5rem 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-shield-alt"></i> Painel de Administração</h1>
                    <p>Bem-vindo, <?php echo htmlspecialchars($user['username']); ?>!</p>
                </div>
                <a href="<?php echo url('logout.php'); ?>" class="btn btn-dark">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
        
        <!-- Estatísticas -->
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
        
        <!-- Ações Rápidas -->
        <div class="quick-actions">
            <h4><i class="fas fa-bolt"></i> Ações Rápidas</h4>
            <a href="<?php echo url('admin-clubes.php'); ?>" class="btn btn-primary">
                <i class="fas fa-futbol"></i> Gerir Clubes
            </a>
            <a href="<?php echo url('admin-jogos.php'); ?>" class="btn btn-primary">
                <i class="fas fa-calendar-alt"></i> Gerir Jogos
            </a>
            <a href="<?php echo url('admin-noticias.php'); ?>" class="btn btn-primary">
                <i class="fas fa-newspaper"></i> Gerir Notícias
            </a>
            <a href="<?php echo url('admin-utilizadores.php'); ?>" class="btn btn-primary">
                <i class="fas fa-users-cog"></i> Gerir Utilizadores
            </a>
            <a href="<?php echo url('index.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i> Voltar ao Site
            </a>
        </div>
        
        <div class="row">
            <!-- Utilizadores Recentes -->
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
            
            <!-- Clubes Mais Populares -->
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
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
