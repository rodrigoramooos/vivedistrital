<?php
require_once __DIR__ . '/../includes/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /vivedistrital/login.php');
    exit;
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica se o método da requisição é POST
    $acao = $_POST['acao'] ?? ''; // Ação a ser realizada, padrão é string vazia
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // INPUT_POST para obter o ID do utilizador e validar como inteiro, FILTER_VALIDATE_INT para garantir que é um número inteiro
    
    if ($user_id && $user_id !== $_SESSION['user_id']) { // Não pode modificar-se a si próprio
        if ($acao === 'toggle_jornalista') { // Ação para alternar privilégios de jornalista
            $is_jornalista = filter_input(INPUT_POST, 'is_jornalista', FILTER_VALIDATE_INT);
            $new_value = $is_jornalista ? 0 : 1; // Alterna entre 0 e 1, dependendo do valor atual, ? é o operador ternário (if else)
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_jornalista = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]); // $new value é o novo valor de is_jornalista, $user_id é o ID do utilizador
            
            $_SESSION['mensagem'] = $new_value ? 'Utilizador promovido a jornalista!' : 'Privilégios de jornalista removidos!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
        
        if ($acao === 'toggle_admin') { // Ação para alternar privilégios de administrador
            $is_admin = filter_input(INPUT_POST, 'is_admin', FILTER_VALIDATE_INT);
            $new_value = $is_admin ? 0 : 1; // Alterna entre 0 e 1, dependendo do valor atual, ? é o operador ternário (if else)
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET is_admin = ? WHERE id = ?");
            $stmt->execute([$new_value, $user_id]); // $new value é o novo valor de is_admin, $user_id é o ID do utilizador
            
            $_SESSION['mensagem'] = $new_value ? 'Utilizador promovido a administrador!' : 'Privilégios de admin removidos!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
    }
    
    header('Location: /vivedistrital/admin/admin-utilizadores.php');
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
$pageCSS = '/vivedistrital/css/admin-utilizadores.css';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
  <div class="admin-header">
    <h1><i class="fas fa-users-cog"></i> Gerir Utilizadores</h1>
    <a href="/vivedistrital/admin/admin.php" class="btn-voltar">
      <i class="fas fa-arrow-left"></i> Voltar
    </a>
  </div>

  <?php if (isset($_SESSION['mensagem'])): // Verifica se há uma mensagem na sessão (mensagem de sucesso, erro, etc.) ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensagem'] ?? 'info'; // Tipo da mensagem, como sucesso, erro, etc. ?>">
      <?php 
        echo $_SESSION['mensagem']; // Exibe a mensagem, mensagem essa que é a definida no bloco de código acima referente ao processamento das ações
        unset($_SESSION['mensagem']); // Remove a mensagem da sessão para não aparecer novamente
        unset($_SESSION['tipo_mensagem']); // Remove o tipo da mensagem da sessão
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
            <td><?php echo htmlspecialchars($u['username']); // Exibe o nome de utilizador, escapando caracteres especiais ?></td>
            <td><?php echo htmlspecialchars($u['email']);  // Exibe o email, escapando caracteres especiais ?></td>
            <td><?php echo $u['clube_favorito_nome'] ? htmlspecialchars($u['clube_favorito_nome']) : '-'; // Exibe o nome do clube favorito, escapando caracteres especiais, ou '-' se não houver clube favorito ?></td>
            <td>
              <?php if ($u['is_admin']): ?>
                <span class="badge badge-admin">ADMIN</span>
              <?php endif; ?>
              <?php if ($u['is_jornalista'] ?? 0): // ?? 0 significa que se is_jornalista não estiver definido, assume 0 ?>
                <span class="badge-jornalista">JORNALISTA</span>
              <?php endif; ?>
              <?php if (!$u['is_admin'] && !($u['is_jornalista'] ?? 0)): // Se não for admin nem jornalista, é USER ?>
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
                    <?php echo ($u['is_jornalista'] ?? 0) ? '✗ Remover Jornalista' : '✓  Tornar Jornalista'; ?>
                  </button>
                </form>

                <!-- Toggle Admin -->
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="acao" value="toggle_admin">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; // id para identificar o utilizador ?>">
                  <input type="hidden" name="is_admin" value="<?php echo $u['is_admin']; ?>">
                  <button type="submit" class="btn-toggle <?php echo $u['is_admin'] ? 'btn-remove' : 'btn-admin'; ?>">
                    <?php echo $u['is_admin'] ? '✗ Remover Admin' : '✓ Tornar Admin'; ?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
