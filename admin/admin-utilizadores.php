<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . url('login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    if ($user_id && $user_id !== $_SESSION['user_id']) {
        if ($acao === 'toggle_jornalista') {
            $is_jornalista = filter_input(INPUT_POST, 'is_jornalista', FILTER_VALIDATE_INT);
            $new_value = $is_jornalista ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_jornalista = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]);
            
            $_SESSION['mensagem'] = $new_value ? 'Jornalista adicionado' : 'Jornalista removido';
            $_SESSION['tipo_mensagem'] = 'success';
        }
        
        if ($acao === 'toggle_admin') {
            $is_admin = filter_input(INPUT_POST, 'is_admin', FILTER_VALIDATE_INT);
            $new_value = $is_admin ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_admin = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]);
            
            $_SESSION['mensagem'] = $new_value ? 'Admin adicionado' : 'Admin removido';
            $_SESSION['tipo_mensagem'] = 'success';
        }
    }
    
    header('Location: ' . url('admin/admin-utilizadores.php'));
    exit;
}

$stmt = $pdo->query("SELECT u.*, c.nome as clube_favorito_nome FROM utilizadores u LEFT JOIN clubes c ON u.clube_favorito_id = c.id ORDER BY u.created_at DESC");
$utilizadores = $stmt->fetchAll();

$pageTitle = 'Gerir Utilizadores';
$pageCSS = 'css/admin-utilizadores.css';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
  <div class="admin-header">
    <h1><i class="fas fa-users-cog"></i> Gerir Utilizadores</h1>
    <a href="<?php echo url('admin/admin.php'); ?>" class="btn-voltar">
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
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="acao" value="toggle_jornalista">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="is_jornalista" value="<?php echo $u['is_jornalista'] ?? 0; ?>">
                  <button type="submit" class="btn-toggle <?php echo ($u['is_jornalista'] ?? 0) ? 'btn-remove' : 'btn-jornalista'; ?>">
                    <?php echo ($u['is_jornalista'] ?? 0) ? 'Remover Jornalista' : 'Tornar Jornalista'; ?>
                  </button>
                </form>

                <form method="POST" style="display: inline;">
                  <input type="hidden" name="acao" value="toggle_admin">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="is_admin" value="<?php echo $u['is_admin']; ?>">
                  <button type="submit" class="btn-toggle <?php echo $u['is_admin'] ? 'btn-remove' : 'btn-admin'; ?>">
                    <?php echo $u['is_admin'] ? 'Remover Admin' : 'Tornar Admin'; ?>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
