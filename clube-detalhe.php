<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/config.php';
require_once 'config-clubes.php';

$pageTitle = 'Detalhes do Clube';
$pageCSS = 'css/clube.css';

$clube_codigo = isset($_GET['id']) ? trim($_GET['id']) : null;

if (!$clube_codigo) {
    header('Location: ' . url('index.php'));
    exit;
}

$clube = getClubeByCodigo($clube_codigo);

if (!$clube) {
    header('Location: ' . url('index.php'));
    exit;
}

$golos_marcados = $clube['golos_marcados'] ?? 0;
$golos_sofridos = $clube['golos_sofridos'] ?? 0;
$diferenca_golos = $golos_marcados - $golos_sofridos;
$posicao = getPosicao($clube['codigo']);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
  <?php include 'includes/topbar.php'; ?>

  <div class="club-header">
    <div class="club-logo">
      <img src="<?php echo url($clube['logo']); ?>" alt="<?php echo $clube['nome']; ?>">
    </div>
    <div class="club-info">
      <h1><?php echo $clube['nome']; ?></h1>
      <p><?php echo $clube['nome']; ?></p>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <h3>Posição</h3>
      <div class="value"><?php echo $posicao; ?>º</div>
    </div>
    <div class="stat-card">
      <h3>Pontos</h3>
      <div class="value"><?php echo $clube['pontos'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
      <h3>Jogos</h3>
      <div class="value"><?php echo $clube['jogos'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
      <h3>Vitórias</h3>
      <div class="value"><?php echo $clube['vitorias'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
      <h3>Empates</h3>
      <div class="value"><?php echo $clube['empates'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
      <h3>Derrotas</h3>
      <div class="value"><?php echo $clube['derrotas'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
      <h3>Golos Marcados</h3>
      <div class="value"><?php echo $golos_marcados; ?></div>
    </div>
    <div class="stat-card">
      <h3>Golos Sofridos</h3>
      <div class="value"><?php echo $golos_sofridos; ?></div>
    </div>
    <div class="stat-card">
      <h3>Diferença de Golos</h3>
      <div class="value" style="color: <?php echo $diferenca_golos >= 0 ? '#27ae60' : '#e74c3c'; ?>">
        <?php echo formatarDG($diferenca_golos); ?>
      </div>
    </div>
  </div>

  <?php if (!empty($clube['forma'])): ?>
  <div style="background: #1A1A1A; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="color: #f1c40f; margin: 0;">Forma Recente</h3>
      <a href="<?php echo url('favoritos.php?clube=' . $clube['codigo']); ?>" style="background: #f1c40f; color: #0D0D0D; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        Ver Jogos <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    <?php echo formatarForma($clube['forma']); ?>
  </div>
  <?php endif; ?>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>