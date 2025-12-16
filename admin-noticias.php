<?php
require_once 'includes/config.php';
header('Content-Type: text/html; charset=utf-8');

// Verificar permissões
if (!canManageNoticias()) {
    header('Location: ' . url('index.php'));
    exit;
}

// Obter todas as notícias
$noticias = getNoticias();

$pageTitle = 'Gerir Notícias';
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

  .nova-noticia-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 2px solid #f1c40f;
  }

  .nova-noticia-card h2 {
    color: #f1c40f;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-group label {
    display: block;
    color: #f1c40f;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .form-group input,
  .form-group textarea,
  .form-group select {
    width: 100%;
    padding: 12px;
    background: #1a1a1a;
    border: 2px solid #3a3a3a;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
  }

  .form-group input:focus,
  .form-group textarea:focus,
  .form-group select:focus {
    outline: none;
    border-color: #f1c40f;
  }

  .form-group textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
  }

  .form-group textarea.conteudo {
    min-height: 200px;
  }

  .btn-criar {
    background: #f1c40f;
    color: #000;
    padding: 12px 30px;
    border-radius: 8px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-criar:hover {
    background: #f39c12;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(241, 196, 15, 0.4);
  }

  .noticias-list {
    background: #1a1a1a;
    border-radius: 16px;
    padding: 2rem;
  }

  .noticias-list h2 {
    color: #fff;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .noticia-item {
    background: #2a2a2a;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }

  .noticia-item:hover {
    border-color: #f1c40f;
  }

  .noticia-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
  }

  .noticia-titulo {
    color: #f1c40f;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
  }

  .noticia-actions {
    display: flex;
    gap: 0.5rem;
  }

  .btn-editar,
  .btn-apagar {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
  }

  .btn-editar {
    background: #3498db;
    color: #fff;
  }

  .btn-editar:hover {
    background: #2980b9;
  }

  .btn-apagar {
    background: #e74c3c;
    color: #fff;
  }

  .btn-apagar:hover {
    background: #c0392b;
  }

  .noticia-meta {
    color: #888;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
  }

  .noticia-resumo {
    color: #ccc;
    margin-bottom: 1rem;
    line-height: 1.5;
  }

  .noticia-edit-form {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #3a3a3a;
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

  .alert-danger {
    background: rgba(231, 76, 60, 0.1);
    border: 2px solid #e74c3c;
    color: #e74c3c;
  }

  @media (max-width: 768px) {
    .admin-container {
      padding: 1rem;
    }

    .admin-header {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }

    .noticia-header {
      flex-direction: column;
      gap: 1rem;
    }

    .noticia-actions {
      width: 100%;
    }

    .btn-editar,
    .btn-apagar {
      flex: 1;
    }
  }
</style>

<div class="admin-container">
  <!-- Header -->
  <div class="admin-header">
    <h1><i class="fas fa-newspaper"></i> Gerir Notícias</h1>
    <a href="<?php echo url(isAdmin() ? 'admin.php' : 'noticias.php'); ?>" class="btn-voltar">
      <i class="fas fa-arrow-left"></i> Voltar
    </a>
  </div>

  <!-- Mensagens -->
  <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensagem'] ?? 'info'; ?>">
      <?php 
        echo $_SESSION['mensagem']; 
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
      ?>
    </div>
  <?php endif; ?>

  <!-- Formulário Nova Notícia -->
  <div class="nova-noticia-card">
    <h2><i class="fas fa-plus-circle"></i> Nova Notícia</h2>
    <form method="POST" action="<?php echo url('admin-noticias-editar.php'); ?>">
      <input type="hidden" name="acao" value="criar">
      
      <div class="form-group">
        <label for="titulo">Título *</label>
        <input type="text" id="titulo" name="titulo" required maxlength="255" placeholder="Ex: União 1919 vence clássico distrital">
      </div>

      <div class="form-group">
        <label for="categoria">Categoria (Ícone)</label>
        <select id="categoria" name="categoria">
          <option value="fire">🔥 Destaque / Fogo</option>
          <option value="shield">🛡️ Defesa / Invencibilidade</option>
          <option value="trophy">🏆 Vitória / Troféu</option>
          <option value="flag">🏁 Chegada / Flag</option>
          <option value="emoji_events">⚽ Futebol / Eventos</option>
          <option value="celebration">🥂 Celebração</option>
          <option value="sports_soccer">⚽ Desporto</option>
        </select>
      </div>

      <div class="form-group">
        <label for="resumo">Conteúdo da Notícia *</label>
        <textarea id="resumo" name="resumo" required placeholder="Escreve aqui a notícia completa (notícias curtas, até 3-4 frases)"></textarea>
      </div>

      <button type="submit" class="btn-criar">
        <i class="fas fa-save"></i> Criar Notícia
      </button>
    </form>
  </div>

  <!-- Lista de Notícias Existentes -->
  <div class="noticias-list">
    <h2><i class="fas fa-list"></i> Notícias Publicadas (<?php echo count($noticias); ?>)</h2>

    <?php if (empty($noticias)): ?>
      <p style="color: #888; text-align: center; padding: 2rem;">Ainda não há notícias publicadas.</p>
    <?php else: ?>
      <?php foreach ($noticias as $noticia): 
        $iconMap = [
          'fire' => 'fa-fire',
          'shield' => 'fa-shield-halved',
          'trophy' => 'fa-trophy',
          'flag' => 'fa-flag-checkered',
          'emoji_events' => 'fa-futbol',
          'celebration' => 'fa-champagne-glasses',
          'sports_soccer' => 'fa-futbol'
        ];
        $icon = $iconMap[$noticia['categoria']] ?? 'fa-newspaper';
        $data_formatada = date('d/m/Y H:i', strtotime($noticia['data_publicacao']));
      ?>
        <div class="noticia-item" id="noticia-<?php echo $noticia['id']; ?>">
          <div class="noticia-header">
            <h3 class="noticia-titulo">
              <i class="fas <?php echo $icon; ?>"></i>
              <?php echo htmlspecialchars($noticia['titulo']); ?>
            </h3>
            <div class="noticia-actions">
              <button class="btn-editar" onclick="toggleEdit(<?php echo $noticia['id']; ?>)">
                <i class="fas fa-edit"></i> Editar
              </button>
              <form method="POST" action="<?php echo url('admin-noticias-editar.php'); ?>" style="display: inline;" onsubmit="return confirm('Tens a certeza que queres apagar esta notícia?');">
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

          <!-- Formulário de Edição (oculto por padrão) -->
          <div class="noticia-edit-form" id="edit-form-<?php echo $noticia['id']; ?>" style="display: none;">
            <form method="POST" action="<?php echo url('admin-noticias-editar.php'); ?>">
              <input type="hidden" name="acao" value="editar">
              <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
              
              <div class="form-group">
                <label>Título</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
              </div>

              <div class="form-group">
                <label>Categoria</label>
                <select name="categoria">
                  <option value="fire" <?php echo $noticia['categoria'] === 'fire' ? 'selected' : ''; ?>>🔥 Destaque</option>
                  <option value="shield" <?php echo $noticia['categoria'] === 'shield' ? 'selected' : ''; ?>>🛡️ Defesa</option>
                  <option value="trophy" <?php echo $noticia['categoria'] === 'trophy' ? 'selected' : ''; ?>>🏆 Vitória</option>
                  <option value="flag" <?php echo $noticia['categoria'] === 'flag' ? 'selected' : ''; ?>>🏁 Flag</option>
                  <option value="emoji_events" <?php echo $noticia['categoria'] === 'emoji_events' ? 'selected' : ''; ?>>⚽ Eventos</option>
                  <option value="celebration" <?php echo $noticia['categoria'] === 'celebration' ? 'selected' : ''; ?>>🥂 Celebração</option>
                  <option value="sports_soccer" <?php echo $noticia['categoria'] === 'sports_soccer' ? 'selected' : ''; ?>>⚽ Desporto</option>
                </select>
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

<?php include 'includes/footer.php'; ?>
