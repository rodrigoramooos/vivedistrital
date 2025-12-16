<?php
require_once 'includes/config.php';
require_once 'config-clubes.php';

$pageTitle = 'Jogos';
$pageCSS = 'css/favoritos.css';

// Verificar se é para clube específico ou favorito
$clube_codigo = isset($_GET['clube']) ? trim($_GET['clube']) : null;
$eh_favorito = false;
$clube_info = null;

if ($clube_codigo) {
    // Buscar clube específico por código
    $clube_info = getClubeByCodigo($clube_codigo);
    if (!$clube_info) {
        header('Location: ' . url('index.php'));
        exit;
    }
    $clube_id = $clube_info['id'];
    $pageTitle = 'Jogos - ' . $clube_info['nome'];
} else {
    // Modo favoritos (requer login)
    if (!isLoggedIn()) {
        header('Location: ' . url('login.php'));
        exit;
    }
    $loggedUser = getLoggedUser();
    if (!$loggedUser || !$loggedUser['clube_favorito_id']) {
        // Sem clube favorito definido
        $clube_id = null;
    } else {
        $clube_id = $loggedUser['clube_favorito_id'];
        $clube_info = getClubeById($clube_id);
        $eh_favorito = true;
    }
}

// Obter jogos
$proximosJogos = [];
$resultadosRecentes = [];

if ($clube_id) {
    $pdo = getDB();
    
    // Próximos jogos
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

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: 20px;
    margin: 2rem 0;
  }

  .empty-state i {
    font-size: 5rem;
    color: #f1c40f;
    margin-bottom: 1.5rem;
    opacity: 0.8;
  }

  .empty-state h3 {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }

  .empty-state p {
    color: #aaa;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
  }

  .empty-state .btn-primary {
    background: #f1c40f !important;
    color: #000 !important;
    border: none !important;
    padding: 0.75rem 2rem !important;
    font-size: 1rem !important;
    font-weight: 700 !important;
    border-radius: 50px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
  }

  .empty-state .btn-primary:hover {
    background: #f39c12 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(241, 196, 15, 0.4) !important;
  }

  .empty-state .btn-primary i {
    color: #000 !important;
    font-size: 1rem !important;
  }

  .favorite-club-banner {
    background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
    padding: 2rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(241, 196, 15, 0.3);
  }

  .favorite-club-banner img {
    height: 80px;
    width: 80px;
    object-fit: contain;
    background: white;
    padding: 10px;
    border-radius: 12px;
  }

  .favorite-club-banner h2 {
    color: #000;
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
  }

  .favorite-club-banner p {
    color: rgba(0, 0, 0, 0.7);
    font-size: 1rem;
    margin: 0.5rem 0 0;
    font-weight: 600;
  }

  .section-title {
    color: #fff;
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .section-title i {
    color: #f1c40f;
  }

  .no-games {
    background: #1a1a1a;
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 2rem;
  }

  .no-games i {
    font-size: 3rem;
    color: #555;
    margin-bottom: 1rem;
  }

  .no-games p {
    color: #888;
    font-size: 1rem;
    margin: 0;
  }

  .match-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
  }

  .match-card:hover {
    background: #2a2a2a;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    border-color: #f1c40f;
  }

  .match-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #2a2a2a;
  }

  .jornada {
    color: #f1c40f;
    font-weight: 700;
    font-size: 0.9rem;
  }

  .data {
    color: #888;
    font-size: 0.9rem;
  }

  .match-teams {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .team {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
  }

  .team:last-child {
    flex-direction: row-reverse;
    text-align: right;
  }

  .team img {
    height: 40px;
    width: 40px;
    object-fit: contain;
  }

  .team span {
    color: #fff;
    font-weight: 600;
    font-size: 1.05rem;
  }

  .vs {
    color: #666;
    font-weight: 700;
    font-size: 1.1rem;
  }

  .score {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.8rem;
    font-weight: 800;
  }

  .score-home, .score-away {
    color: #f1c40f;
  }

  .score-separator {
    color: #555;
  }

  .match-card.finished {
    border-left: 4px solid #f1c40f;
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
    .match-teams {
      flex-direction: column;
      gap: 0.5rem;
    }
    .team {
      width: 100%;
      justify-content: flex-start;
    }
    .team:last-child {
      flex-direction: row;
      text-align: left;
    }
  }
</style>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include 'includes/topbar.php'; ?>

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
      <!-- Banner especial para clube favorito -->
      <div class="favorite-club-banner">
        <img src="<?php echo url($clube_info['logo']); ?>" alt="<?php echo htmlspecialchars($clube_info['nome']); ?>">
        <div>
          <h2><?php echo htmlspecialchars($clube_info['nome']); ?></h2>
          <p><i class="fas fa-star"></i> O teu clube favorito</p>
        </div>
      </div>
    <?php else: ?>
      <!-- Cabeçalho simples para clube específico -->
      <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; padding: 1.5rem; background: #1a1a1a; border-radius: 12px;">
        <img src="<?php echo url($clube_info['logo']); ?>" alt="<?php echo htmlspecialchars($clube_info['nome']); ?>" style="height: 60px; width: 60px; object-fit: contain;">
        <h2 style="color: #fff; margin: 0; font-size: 1.8rem; font-weight: 700;"><?php echo htmlspecialchars($clube_info['nome']); ?></h2>
      </div>
    <?php endif; ?>

    <!-- Próximos Jogos -->
    <h5 class="section-title">
      <i class="fas fa-calendar-alt"></i> Próximos Jogos
    </h5>

    <?php if (empty($proximosJogos)): ?>
      <div class="no-games">
        <i class="fas fa-calendar-times"></i>
        <p>Não há próximos jogos agendados</p>
      </div>
    <?php else: ?>
      <?php foreach ($proximosJogos as $jogo): ?>
        <div class="match-card">
          <div class="match-header">
            <span class="jornada"><?php echo $jogo['jornada']; ?>ª Jornada</span>
            <span class="data"><?php echo date('d/m/Y H:i', strtotime($jogo['data_jogo'])); ?></span>
          </div>
          <div class="match-teams">
            <div class="team">
              <img src="<?php echo url($jogo['clube_casa_logo']); ?>" alt="<?php echo htmlspecialchars($jogo['clube_casa_nome']); ?>">
              <span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span>
            </div>
            <span class="vs">VS</span>
            <div class="team">
              <span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span>
              <img src="<?php echo url($jogo['clube_fora_logo']); ?>" alt="<?php echo htmlspecialchars($jogo['clube_fora_nome']); ?>">
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
              <img src="<?php echo url($jogo['clube_casa_logo']); ?>" alt="<?php echo htmlspecialchars($jogo['clube_casa_nome']); ?>">
              <span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span>
            </div>
            <div class="score">
              <span class="score-home"><?php echo $jogo['resultado_casa']; ?></span>
              <span class="score-separator">-</span>
              <span class="score-away"><?php echo $jogo['resultado_fora']; ?></span>
            </div>
            <div class="team">
              <span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span>
              <img src="<?php echo url($jogo['clube_fora_logo']); ?>" alt="<?php echo htmlspecialchars($jogo['clube_fora_nome']); ?>">
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
