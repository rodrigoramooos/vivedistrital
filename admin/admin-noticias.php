<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: text/html; charset=utf-8');

if (!canManageNoticias()) {
    header('Location: ' . url('index.php'));
    exit;
}

$noticias = getNoticias();

$pageTitle = 'Gerir Notícias';
$pageCSS = 'css/admin-noticias.css';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
  <div class="admin-header">
    <h1><i class="fas fa-newspaper"></i> Gerir Notícias</h1>
    <a href="<?php echo url(isAdmin() ? 'admin/admin.php' : 'noticias.php'); ?>" class="btn-voltar">
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

  <div class="nova-noticia-card">
    <h2><i class="fas fa-plus-circle"></i> Nova Notícia</h2>
    <form method="POST" action="<?php echo url('admin/admin-noticias-editar.php'); ?>">
      <input type="hidden" name="acao" value="criar">
      
      <div class="form-group">
        <label for="titulo">Título *</label>
        <input type="text" id="titulo" name="titulo" required maxlength="255">
      </div>

      <div class="form-group">
        <label for="resumo">Conteúdo da Notícia *</label>
        <textarea id="resumo" name="resumo" required></textarea>
      </div>

      <button type="submit" class="btn-criar">
        <i class="fas fa-save"></i> Criar Notícia
      </button>
    </form>
  </div>

  <div class="noticias-list">
    <h2><i class="fas fa-list"></i> Notícias Publicadas (<?php echo count($noticias); ?>)</h2>

    <?php if (empty($noticias)): ?>
      <p style="color: #888; text-align: center; padding: 2rem;">Ainda não há notícias publicadas.</p>
    <?php else: ?>
      <?php foreach ($noticias as $noticia): 
        $data_formatada = date('d/m/Y H:i', strtotime($noticia['data_publicacao']));
      ?>
        <div class="noticia-item" id="noticia-<?php echo $noticia['id']; ?>">
          <div class="noticia-header">
            <h3 class="noticia-titulo">
              <?php echo htmlspecialchars($noticia['titulo']); ?>
            </h3>
            <div class="noticia-actions">
              <button class="btn-editar" onclick="toggleEdit(<?php echo $noticia['id']; ?>)">
                <i class="fas fa-edit"></i> Editar
              </button>
              <form method="POST" action="<?php echo url('admin/admin-noticias-editar.php'); ?>" style="display: inline;" onsubmit="return confirm('Tens a certeza que queres apagar esta notícia?');">
                <input type="hidden" name="acao" value="apagar">
                <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                <button type="submit" class="btn-apagar">
                  <i class="fas fa-trash"></i> Apagar
                </button>
              </form>
            </div>
          </div>

          <div class="noticia-meta">
            Publicado a <?php echo $data_formatada; ?> por <strong><?php echo htmlspecialchars($noticia['autor_nome']); ?></strong>
          </div>

          <div class="noticia-resumo">
            <?php echo htmlspecialchars($noticia['resumo']); ?>
          </div>

          <div class="noticia-edit-form" id="edit-form-<?php echo $noticia['id']; ?>" style="display: none;">
            <form method="POST" action="<?php echo url('admin/admin-noticias-editar.php'); ?>">
              <input type="hidden" name="acao" value="editar">
              <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
              
              <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
              </div>

              <div class="form-group">
                <label>Conteúdo da Notícia</label>
                <textarea name="resumo" required><?php echo htmlspecialchars($noticia['resumo']); ?></textarea>
              </div>

              <button type="submit" class="btn-criar">
                <i class="fas fa-save"></i> Guardar Alterações
              </button>
              <button type="button" class="btn-voltar" onclick="toggleEdit(<?php echo $noticia['id']; ?>)" style="margin-left: 10px;">
                Cancelar
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleEdit(noticiaId) {
  const form = document.getElementById('edit-form-' + noticiaId);
  if (form.style.display === 'none') {
    form.style.display = 'block';
  } else {
    form.style.display = 'none';
  }
}
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
