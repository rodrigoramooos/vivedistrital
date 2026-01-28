<!-- Barra Lateral -->
<div class="sidebar">
  <h1><img src="/vivedistrital/imgs/logos/Logo-ViveDistrital-LetrasBrancas.png" alt="Logo Vive Distrital"></h1>
  
  <nav class="nav flex-column gap-2">
    <a href="/vivedistrital/index.php" class="nav-link">
      <span class="icone">home</span>Página Inicial
    </a>

    <a href="/vivedistrital/classificacoes.php" class="nav-link">
      <span class="icone">bar_chart</span>Classificações
    </a>

    <a href="/vivedistrital/favoritos.php" class="nav-link">
      <span class="icone">star</span>Favoritos
    </a>

    <a href="/vivedistrital/noticias.php" class="nav-link">
      <span class="icone">article</span>Notícias
    </a>
  </nav>

    <?php if (isLoggedIn()): ?> <!-- isLoggedIn() verifica se o utilizador está autenticado -->
      <?php 
      $loggedUser = getLoggedUser(); // getLoggedUser() obtém os dados do utilizador logado
      if ($loggedUser && $loggedUser['clube_favorito_id']): // $loggedUser && $loggedUser['clube_favorito_id'] verifica se o utilizador tem clube favorito; && é um "e" lógico
      ?>
        <p class="favorites-label">Clube Favorito</p>
        <a href="/vivedistrital/clube-detalhe.php?id=<?php echo $loggedUser['clube_favorito_codigo']; ?>" class="nav-link">
          <span class="icone">sports_soccer</span><?php echo htmlspecialchars($loggedUser['clube_favorito_nome']); ?>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </nav>
</div>
