<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config-clubes.php';

$pageTitle = 'Jogos';
$pageCSS = '/vivedistrital/css/favoritos.css';

// Verificar se é para clube específico ou favorito
// Se não existir, usar clube favorito do utilizador logado
$clube_codigo = isset($_GET['clube']) ? trim($_GET['clube']) : null;
$eh_favorito = false; // Indica se estamos a ver o clube favorito do utilizador
$clube_info = null; // Armazenar as informações do clube a mostrar

if ($clube_codigo) {
    // Buscar clube específico por código
    // Buscar dados do clube
    $clube_info = getClubeByCodigo($clube_codigo);
    if (!$clube_info) {
        header('Location: /vivedistrital/index.php');
        exit;
    }
    $clube_id = $clube_info['id'];
    $pageTitle = 'Jogos - ' . $clube_info['nome'];
} else {
    // Modo favoritos (requer login)
    if (!isLoggedIn()) {
        header('Location: /vivedistrital/login.php');
        exit;
    }
    $loggedUser = getLoggedUser();
    if (!$loggedUser || !$loggedUser['clube_favorito_id']) {
        // Sem clube favorito definido
        $clube_id = null;
    } else {
        $clube_id = $loggedUser['clube_favorito_id']; // Obter ID do clube favorito
        $clube_info = getClubeById($clube_id); // Buscar dados do clube favorito
        $eh_favorito = true; // Apresenta o clube favorito
    }
}

// Obter jogos
$proximosJogos = [];
$resultadosRecentes = [];

// Se houver clube definido, buscar jogos relacionados
if ($clube_id) {
    $pdo = getDB();
    
    // Próximos jogos
    // join com clubes para obter nomes e logos, inner join não é usado porque pode não haver jogos
    // garante que só aparecem jogos do clube selecionado através do WHERE
    $stmt = $pdo->prepare("
        SELECT j.*, 
               cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
               cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
        FROM jogos j
        JOIN clubes cc ON j.clube_casa_id = cc.id
        JOIN clubes cf ON j.clube_fora_id = cf.id
        WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?)
        AND j.status = 'agendado'
        ORDER BY j.data_jogo ASC
        LIMIT 10
    ");
    $stmt->execute([$clube_id, $clube_id]);
    $proximosJogos = $stmt->fetchAll();
    
    // Resultados recentes
    // garante que só aparecem jogos do clube selecionado através do WHERE
    $stmt = $pdo->prepare("
        SELECT j.*, 
               cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
               cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
        FROM jogos j
        JOIN clubes cc ON j.clube_casa_id = cc.id
        JOIN clubes cf ON j.clube_fora_id = cf.id
        WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?)
        AND j.status = 'finalizado'
        ORDER BY j.data_jogo DESC
        LIMIT 10
    ");
    $stmt->execute([$clube_id, $clube_id]);
    $resultadosRecentes = $stmt->fetchAll();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include __DIR__ . '/includes/topbar.php'; ?>

  <?php if (!$clube_info): ?>
    <!-- Mensagem quando não tem clube -->
    <div class="empty-state">
      <i class="fas fa-star"></i>
      <h3>Ainda não tens favoritos selecionados</h3>
      <p>Seleciona um clube favorito nas definições do teu perfil</p>
    </div>
  <?php else: ?>
    <!-- Mostrar clube (favorito ou específico) -->
    <?php if ($eh_favorito): ?>
      <!-- Banner para clube favorito -->
      <div class="favorite-club-banner">
        <img src="/vivedistrital/<?php echo $clube_info['logo']; ?>" alt="<?php echo htmlspecialchars($clube_info['nome']); ?>">
        <div>
          <h2><?php echo htmlspecialchars($clube_info['nome']); ?></h2>
          <p><i class="fas fa-star"></i> O teu clube favorito</p>
        </div>
      </div>
    <?php else: ?>
      <!-- Cabeçalho simples para clube específico -->
      <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; padding: 1.5rem; background: #1a1a1a; border-radius: 12px;">
        <img src="/vivedistrital/<?php echo $clube_info['logo']; ?>" alt="<?php echo htmlspecialchars($clube_info['nome']); ?>" style="height: 60px; width: 60px; object-fit: contain;">
        <h2 style="color: #fff; margin: 0; font-size: 1.8rem; font-weight: 700;"><?php echo htmlspecialchars($clube_info['nome']); ?></h2>
      </div>
    <?php endif; ?>

    <!-- Próximos Jogos -->
    <h5 class="section-title">
      <i class="fas fa-calendar-alt"></i> Próximos Jogos
    </h5>

    <?php if (empty($proximosJogos)): // Se não houver próximos jogos ?>
      <div class="no-games">
        <i class="fas fa-calendar-times"></i>
        <p>Não há próximos jogos agendados</p>
      </div>
    <?php else: ?>
      <?php foreach ($proximosJogos as $jogo): // Navegar pelos próximos jogos ?>
        <div class="match-card">
          <div class="match-header">
            <span class="jornada"><?php echo $jogo['jornada']; ?>ª Jornada</span> <!-- Mostrar a jornada que vem da base de dados -->
            <span class="data"><?php echo date('d/m/Y H:i', strtotime($jogo['data_jogo'])); ?></span> <!-- strtotime converte a data para timestamp -->
          </div>
          <div class="match-teams">
            <div class="team">
              <img src="/vivedistrital/<?php echo $jogo['clube_casa_logo']; ?>" alt="<?php echo htmlspecialchars($jogo['clube_casa_nome']); ?>">
              <span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span>
            </div>
            <span class="vs">VS</span>
            <div class="team">
              <span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span>
              <img src="/vivedistrital/<?php echo $jogo['clube_fora_logo']; ?>" alt="<?php echo htmlspecialchars($jogo['clube_fora_nome']); ?>">
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Resultados Recentes -->
    <h5 class="section-title mt-4">
      <i class="fas fa-history"></i> Resultados Recentes
    </h5>

    <?php if (empty($resultadosRecentes)): ?>
      <div class="no-games">
        <i class="fas fa-inbox"></i>
        <p>Não há resultados recentes</p>
      </div>
    <?php else: ?>
      <?php foreach ($resultadosRecentes as $jogo): ?>
        <div class="match-card finished">
          <div class="match-header">
            <span class="jornada"><?php echo $jogo['jornada']; ?>ª Jornada</span>
            <span class="data"><?php echo date('d/m/Y', strtotime($jogo['data_jogo'])); ?></span>
          </div>
          <div class="match-teams">
            <div class="team"> 
              <!-- array $jogo mostra os dados do jogo -->
              <img src="/vivedistrital/<?php echo $jogo['clube_casa_logo']; ?>" alt="<?php echo htmlspecialchars($jogo['clube_casa_nome']); ?>">
              <span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span>
            </div>
            <div class="score">
              <span class="score-home"><?php echo $jogo['resultado_casa']; ?></span>
              <span class="score-separator">-</span>
              <span class="score-away"><?php echo $jogo['resultado_fora']; ?></span>
            </div>
            <div class="team">
              <span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span>
              <img src="/vivedistrital/<?php echo $jogo['clube_fora_logo']; ?>" alt="<?php echo htmlspecialchars($jogo['clube_fora_nome']); ?>">
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
