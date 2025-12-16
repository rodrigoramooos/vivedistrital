<?php
require_once 'includes/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . url('login.php'));
    exit;
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    if ($user_id && $user_id !== $_SESSION['user_id']) { // Não pode modificar-se a si próprio
        if ($acao === 'toggle_jornalista') {
            $is_jornalista = filter_input(INPUT_POST, 'is_jornalista', FILTER_VALIDATE_INT);
            $new_value = $is_jornalista ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_jornalista = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]);
            
            $_SESSION['mensagem'] = $new_value ? 'Utilizador promovido a jornalista!' : 'Privilégios de jornalista removidos!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
        
        if ($acao === 'toggle_admin') {
            $is_admin = filter_input(INPUT_POST, 'is_admin', FILTER_VALIDATE_INT);
            $new_value = $is_admin ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_admin = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]);
            
            $_SESSION['mensagem'] = $new_value ? 'Utilizador promovido a administrador!' : 'Privilégios de admin removidos!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
    }
    
    header('Location: ' . url('admin-utilizadores.php'));
    exit;
}

// Obter todos os utilizadores
$stmt = $pdo->query("
    SELECT u.*, c.nome as clube_favorito_nome 
    FROM utilizadores u 
    LEFT JOIN clubes c ON u.clube_favorito_id = c.id 
    ORDER BY u.created_at DESC
");
$utilizadores = $stmt->fetchAll();

$pageTitle = 'Gerir Utilizadores';
include 'includes/header.php';
?>

<style>
  body {
    background: #0D0D0D;
    color: #fff;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
  }

  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1c40f;
  }

  .admin-header h1 {
    color: #f1c40f;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
  }

  .btn-voltar {
    background: #2a2a2a;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
  }

  .btn-voltar:hover {
    background: #3a3a3a;
    color: #f1c40f;
  }

  .users-table {
    background: #1a1a1a;
    border-radius: 16px;
    padding: 2rem;
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    color: #f1c40f;
    font-weight: 700;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid #3a3a3a;
  }

  td {
    padding: 1rem;
    border-bottom: 1px solid #2a2a2a;
    color: #ccc;
  }

  tr:hover {
    background: #2a2a2a;
  }

  .badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
    margin: 2px;
  }

  .badge-admin {
    background: #e74c3c;
    color: #fff;
  }

  .badge-jornalista {
    background: #3498db;
    color: #fff;
  }

  .badge-user {
    background: #555;
    color: #ccc;
  }

  .btn-toggle {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85rem;
    margin: 2px;
    transition: all 0.3s ease;
  }

  .btn-jornalista {
    background: #3498db;
    color: #fff;
  }

  .btn-jornalista:hover {
    background: #2980b9;
  }

  .btn-admin {
    background: #e74c3c;
    color: #fff;
  }

  .btn-admin:hover {
    background: #c0392b;
  }

  .btn-remove {
    background: #555;
    color: #fff;
  }

  .btn-remove:hover {
    background: #666;
  }

  .alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 600;
  }

  .alert-success {
    background: rgba(46, 204, 113, 0.1);
    border: 2px solid #2ecc71;
    color: #2ecc71;
  }
</style>

<div class="admin-container">
  <div class="admin-header">
    <h1><i class="fas fa-users-cog"></i> Gerir Utilizadores</h1>
    <a href="<?php echo url('admin.php'); ?>" class="btn-voltar">
      <i class="fas fa-arrow-left"></i> Voltar
    </a>
  </div>

  <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensagem'] ?? 'info'; ?>">
      <?php 
        echo $_SESSION['mensagem']; 
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
      ?>
    </div>
  <?php endif; ?>

  <div class="users-table">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Clube Favorito</th>
          <th>Tipo</th>
          <th>Registado</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($utilizadores as $u): ?>
          <tr>
            <td>#<?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo $u['clube_favorito_nome'] ? htmlspecialchars($u['clube_favorito_nome']) : '-'; ?></td>
            <td>
              <?php if ($u['is_admin']): ?>
                <span class="badge badge-admin">ADMIN</span>
              <?php endif; ?>
              <?php if ($u['is_jornalista'] ?? 0): ?>
                <span class="badge-jornalista">JORNALISTA</span>
              <?php endif; ?>
              <?php if (!$u['is_admin'] && !($u['is_jornalista'] ?? 0)): ?>
                <span class="badge badge-user">USER</span>
              <?php endif; ?>
            </td>
            <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
            <td>
              <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                <!-- Toggle Jornalista -->
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="acao" value="toggle_jornalista">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="is_jornalista" value="<?php echo $u['is_jornalista'] ?? 0; ?>">
                  <button type="submit" class="btn-toggle <?php echo ($u['is_jornalista'] ?? 0) ? 'btn-remove' : 'btn-jornalista'; ?>">
                    <?php echo ($u['is_jornalista'] ?? 0) ? '✗ Remover Jornalista' : '📰 Tornar Jornalista'; ?>
                  </button>
                </form>

                <!-- Toggle Admin -->
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="acao" value="toggle_admin">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="is_admin" value="<?php echo $u['is_admin']; ?>">
                  <button type="submit" class="btn-toggle <?php echo $u['is_admin'] ? 'btn-remove' : 'btn-admin'; ?>">
                    <?php echo $u['is_admin'] ? '✗ Remover Admin' : '🛡️ Tornar Admin'; ?>
                  </button>
                </form>
              <?php else: ?>
                <span style="color: #888;">(Você)</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
