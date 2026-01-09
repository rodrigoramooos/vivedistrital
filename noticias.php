<?php
require_once 'includes/config.php';
header('Content-Type: text/html; charset=utf-8');

$pageTitle = 'Notícias';
$pageCSS = 'css/noticias.css';

$noticias = getNoticias();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
  <?php include 'includes/topbar.php'; ?>

  <?php if (canManageNoticias()): ?>
    <div class="mb-3">
      <a href="<?php echo url('admin/admin-noticias.php'); ?>" class="btn btn-warning" style="background: #f1c40f; color: #000; border: none; font-weight: 600;">
        <i class="fas fa-edit"></i> Gerir Notícias
      </a>
    </div>
  <?php endif; ?>

  <h4 class="mb-3" style="color: #fff; font-weight: 700;">Notícias Recentes</h4>

  <?php if (empty($noticias)): ?>
    <div class="card-noticia" style="text-align: center; padding: 3rem;">
      <i class="fas fa-newspaper" style="font-size: 3rem; color: #555; margin-bottom: 1rem;"></i>
      <p style="color: #888; font-size: 1.1rem;">Ainda não há notícias publicadas.</p>
    </div>
  <?php else: ?>
    <?php foreach ($noticias as $noticia): 
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
          <?php echo htmlspecialchars($noticia['titulo']); ?>
        </h5>
        <p class="mb-2" style="color: #ccc; font-size: 1rem;"><?php echo htmlspecialchars($noticia['resumo']); ?></p>
        <p class="mb-0">Publicado a <?php echo $data_formatada; ?> por <strong><?php echo htmlspecialchars($noticia['autor_nome']); ?></strong></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
