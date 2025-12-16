<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/config.php';
require_once 'config-clubes.php';

$pageTitle = 'Detalhes do Clube';
$pageCSS = 'css/clube-detalhe.css';

// Obter código do clube da URL
$clube_codigo = isset($_GET['id']) ? trim($_GET['id']) : null;

// Se não houver código, redirecionar
if (!$clube_codigo) {
    header('Location: ' . url('index.php'));
    exit;
}

// Buscar dados do clube
$clube = getClubeByCodigo($clube_codigo);

// Se clube não existe, redirecionar
if (!$clube) {
    header('Location: ' . url('index.php'));
    exit;
}

// Calcular diferença de golos e posição
$golos_marcados = $clube['golos_marcados'] ?? 0;
$golos_sofridos = $clube['golos_sofridos'] ?? 0;
$diferenca_golos = $golos_marcados - $golos_sofridos;
$posicao = getPosicao($clube['codigo']);

// Estruturar dados para o template
$clube_data = [
    'nome' => $clube['nome'],
    'descricao' => $clube['nome'],
    'logo' => $clube['logo'],
    'posicao' => $posicao,
    'pontos' => $clube['pontos'] ?? 0,
    'jogos' => $clube['jogos'] ?? 0,
    'vitorias' => $clube['vitorias'] ?? 0,
    'empates' => $clube['empates'] ?? 0,
    'derrotas' => $clube['derrotas'] ?? 0,
    'golos_marcados' => $golos_marcados,
    'golos_sofridos' => $golos_sofridos,
    'diferenca_golos' => $diferenca_golos,
    'forma' => $clube['forma'] ?? ''
];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
  .forma {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  
  .forma span {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
  }
  
  .forma .vitoria {
    background: #27ae60;
    color: #fff;
  }
  
  .forma .empate {
    background: #f39c12;
    color: #fff;
  }
  
  .forma .derrota {
    background: #e74c3c;
    color: #fff;
  }
  
  .forma .nao-jogado {
    background: #555;
    color: #999;
  }
</style>

<style>
  .club-header {
    background-color: #1A1A1A;
    border-radius: 8px;
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
  }

  .club-logo {
    width: 150px;
    height: 150px;
    background-color: #2a2a2a;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  .club-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .club-info h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #FFFFFF;
  }

  .club-info p {
    color: #888;
    font-size: 1rem;
    margin: 0.5rem 0;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .stat-card {
    background-color: #1A1A1A;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
  }

  .stat-card h3 {
    color: #888;
    font-size: 0.9rem;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
  }

  .stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-accent);
  }

  @media (max-width: 768px) {
    .sidebar {
      position: static;
      width: 100%;
      height: auto;
    }
    .main-content {
      margin-left: 0;
      padding: 1rem;
    }
    .club-header {
      flex-direction: column;
      text-align: center;
    }
    .club-info h1 {
      font-size: 1.8rem;
    }
  }
</style>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include 'includes/topbar.php'; ?>

  <!-- Cabeçalho do Clube -->
  <div class="club-header">
    <div class="club-logo">
      <img src="<?php echo url($clube_data['logo']); ?>" alt="<?php echo $clube_data['nome']; ?>">
    </div>
    <div class="club-info">
      <h1><?php echo $clube_data['nome']; ?></h1>
      <p><?php echo $clube_data['descricao']; ?></p>
    </div>
  </div>

  <!-- Estatísticas -->
  <div class="stats-grid">
    <div class="stat-card">
      <h3>Posição</h3>
      <div class="value"><?php echo $clube_data['posicao']; ?>º</div>
    </div>
    <div class="stat-card">
      <h3>Pontos</h3>
      <div class="value"><?php echo $clube_data['pontos']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Jogos</h3>
      <div class="value"><?php echo $clube_data['jogos']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Vitórias</h3>
      <div class="value"><?php echo $clube_data['vitorias']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Empates</h3>
      <div class="value"><?php echo $clube_data['empates']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Derrotas</h3>
      <div class="value"><?php echo $clube_data['derrotas']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Golos Marcados</h3>
      <div class="value"><?php echo $clube_data['golos_marcados']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Golos Sofridos</h3>
      <div class="value"><?php echo $clube_data['golos_sofridos']; ?></div>
    </div>
    <div class="stat-card">
      <h3>Diferença de Golos</h3>
      <div class="value" style="color: <?php echo $clube_data['diferenca_golos'] >= 0 ? '#27ae60' : '#e74c3c'; ?>">
        <?php echo formatarDG($clube_data['diferenca_golos']); ?>
      </div>
    </div>
  </div>

  <?php if (!empty($clube_data['forma'])): ?>
  <!-- Forma Recente -->
  <div style="background: #1A1A1A; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="color: #f1c40f; margin: 0;">Forma Recente</h3>
      <a href="<?php echo url('favoritos.php?clube=' . $clube['codigo']); ?>" style="background: #f1c40f; color: #0D0D0D; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
        Ver Jogos <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    <?php echo formatarForma($clube_data['forma']); ?>
  </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
