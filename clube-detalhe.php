<?php

// require ≠ include, pois o require impede que o arquivo seja carregado mais que 1x, e gera um erro fatal se o arquivo não for encontrado, o include apenas um aviso.

// Configurações iniciais
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config-clubes.php';

$pageTitle = 'Detalhes do Clube';
$pageCSS = '/vivedistrital/css/clube.css';

// Obter código do clube da URL
$clube_codigo = isset($_GET['id']) ? trim($_GET['id']) : null;

// Se não houver código, redirecionar para a página inicial
if (!$clube_codigo) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Buscar dados do clube pelo código
$clube = getClubeByCodigo($clube_codigo);

// Se clube não existe, redirecionar para a página inicial, pois tem o (!) que indica negação
if (!$clube) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Calcular diferença de golos e posição
$golos_marcados = $clube['golos_marcados'] ?? 0;
$golos_sofridos = $clube['golos_sofridos'] ?? 0;
$diferenca_golos = $golos_marcados - $golos_sofridos;
$posicao = getPosicao($clube['codigo']);

// Estruturar dados para o layout de cada clube
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

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include __DIR__ . '/includes/topbar.php'; ?>

  <!-- Cabeçalho do Clube -->
  <div class="club-header">
    <div class="club-logo">
      <!-- Usar caminho absoluto para a imagem do logo, echo serve para apresentar o valor -->
      <img src="/vivedistrital/<?php echo $clube_data['logo']; ?>" alt="<?php echo $clube_data['nome']; ?>">
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
      <!-- Cor verde para positivo, vermelho para negativo -->
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
      <a href="/vivedistrital/favoritos.php?clube=<?php echo $clube['codigo']; ?>" style="background: #f1c40f; color: #0D0D0D; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;"> 
        Ver Jogos <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    <?php echo formatarForma($clube_data['forma']); ?> <!-- Função para formatar a forma recente -->
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
