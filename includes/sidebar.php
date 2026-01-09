<div class="sidebar">
  <h1><img src="<?php echo url('imgs/logos/Logo-ViveDistrital-LetrasBrancas.png'); ?>" alt="Logo Vive Distrital"></h1>
  
  <nav class="nav flex-column gap-2">
    <a href="<?php echo url('index.php'); ?>" class="nav-link <?php echo isActive('index.php'); ?>">
      <span class="icone">home</span>Página Inicial
    </a>
    <a href="<?php echo url('classificacoes.php'); ?>" class="nav-link <?php echo isActive('classificacoes.php'); ?>">
      <span class="icone">bar_chart</span>Classificações
    </a>
    <a href="<?php echo url('favoritos.php'); ?>" class="nav-link <?php echo isActive('favoritos.php'); ?>">
      <span class="icone">star</span>Favoritos
    </a>
    <a href="<?php echo url('noticias.php'); ?>" class="nav-link <?php echo isActive('noticias.php'); ?>">
      <span class="icone">article</span>Notícias
    </a>
    
    <?php if (isLoggedIn()): ?>
      <?php $loggedUser = getLoggedUser(); ?>
      <?php if ($loggedUser && $loggedUser['clube_favorito_id']): ?>
        <p class="favorites-label">Clube Favorito</p>
        <a href="<?php echo url('clube-detalhe.php?id=' . $loggedUser['clube_favorito_codigo']); ?>" class="nav-link">
          <span class="icone">sports_soccer</span><?php echo htmlspecialchars($loggedUser['clube_favorito_nome']); ?>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </nav>
</div>
