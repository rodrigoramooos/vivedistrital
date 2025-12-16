<?php
require_once 'includes/config.php';
header('Content-Type: text/html; charset=utf-8');

$pageTitle = 'Notícias';

// Obter notícias da base de dados
$noticias = getNoticias();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
  .card-noticia {
    background-color: #1A1A1A;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    border: 2px solid transparent;
  }

  .card-noticia h5 {
    color: #FFFFFF;
    margin-bottom: 0.5rem;
  }

  .card-noticia p {
    color: #999999;
    font-size: 0.9rem;
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
  }
</style>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include 'includes/topbar.php'; ?>

  <!-- Botão Gerir Notícias (apenas para admins e jornalistas) -->
  <?php if (canManageNoticias()): ?>
    <div class="mb-3">
      <a href="<?php echo url('admin-noticias.php'); ?>" class="btn btn-warning" style="background: #f1c40f; color: #000; border: none; font-weight: 600;">
        <i class="fas fa-edit"></i> Gerir Notícias
      </a>
    </div>
  <?php endif; ?>

  <!-- Notícias -->
  <h4 class="mb-3" style="color: #fff; font-weight: 700;">Notícias Recentes</h4>

  <?php if (empty($noticias)): ?>
    <div class="card-noticia" style="text-align: center; padding: 3rem;">
      <i class="fas fa-newspaper" style="font-size: 3rem; color: #555; margin-bottom: 1rem;"></i>
      <p style="color: #888; font-size: 1.1rem;">Ainda não há notícias publicadas.</p>
    </div>
  <?php else: ?>
    <?php foreach ($noticias as $noticia): 
      // Mapear ícones
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
      $data_formatada = date('d \d\e F \d\e Y', strtotime($noticia['data_publicacao']));
      $meses = [
        'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
        'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
        'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
        'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
      ];
      $data_formatada = str_replace(array_keys($meses), array_values($meses), $data_formatada);
    ?>
      <div class="card-noticia">
        <h5 class="mb-2">
          <i class="fa-solid <?php echo $icon; ?> me-2 text-warning"></i><?php echo htmlspecialchars($noticia['titulo']); ?>
        </h5>
        <p class="mb-2" style="color: #ccc; font-size: 1rem;"><?php echo htmlspecialchars($noticia['resumo']); ?></p>
        <p class="mb-0">Publicado a <?php echo $data_formatada; ?> por <strong><?php echo htmlspecialchars($noticia['autor_nome']); ?></strong></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
