<?php
// PÁGINA INICIAL - VIVE DISTRITAL

// Configurar encoding UTF-8
header('Content-Type: text/html; charset=utf-8');

// Importar dependências do sistema // Conexão BD e sessões
require_once __DIR__ . '/includes/config.php'; 

$pageTitle = 'Página Inicial';
$pageCSS = '/vivedistrital/css/pagina-inicial.css';

// Configurar metadados da página
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

  <div class="main-content">

    <?php include __DIR__ . '/includes/topbar.php'; ?>

    <!-- Banner + Jogo em Destaque -->
    <div class="row mb-4 g-3">
      <div class="col-lg-8">
        <!-- Banner Carousel (função para navegar/slideshow de imagens) -->
        <div class="banner-wrapper">
          <img id="bannerImage" src="/vivedistrital/imgs/logos/banner-ecra-principal.png" alt="Banner" class="img-fluid rounded" style="width: 100%;">
          
          <!-- Botões clicáveis dos banners (overlays invisíveis) -->
          <a href="/vivedistrital/registo.php" id="bannerButton2" class="banner-cta-overlay" style="display: none;" aria-label="Criar Conta Grátis"></a>
          <a href="/vivedistrital/noticias.php" id="bannerButton3" class="banner-cta-overlay" style="display: none;" aria-label="Ver Notícias"></a>
          
          <button class="banner-nav banner-prev" onclick="changeBanner(-1)" aria-label="Imagem anterior">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="banner-nav banner-next" onclick="changeBanner(1)" aria-label="Próxima imagem">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card jogo-destaque">
          <div class="card-body">
            <h5 class="card-title">Jogo em Destaque</h5>
            
            <?php
            // Buscar jogo em destaque
            try {
              // inner join para obter nomes e logos dos clubes
              // pdo e stmt são da conexão incluída em includes/config.php
              $stmt = $pdo->prepare("
                SELECT j.*, 
                       cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
                       cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
                FROM jogos j
                INNER JOIN clubes cc ON j.clube_casa_id = cc.id
                INNER JOIN clubes cf ON j.clube_fora_id = cf.id
                WHERE j.destaque = 1
                LIMIT 1
              ");
              $stmt->execute();
              $jogo_destaque = $stmt->fetch(); // "Busca" o primeiro resultado
              
              if ($jogo_destaque):
                // Gera estatísticas aleatórias
                $remates_enq_casa = rand(0, 9);
                $remates_enq_fora = rand(0, 9);
                $total_remates_casa = rand(0, 15);
                $total_remates_fora = rand(0, 15);
                $faltas_casa = rand(0, 22);
                $faltas_fora = rand(0, 22);
                
                // Calcular percentagens para barras
                $total_remates = $total_remates_casa + $total_remates_fora;
                $perc_remates_casa = $total_remates > 0 ? ($total_remates_casa / $total_remates) * 100 : 50;
                $perc_remates_fora = $total_remates > 0 ? ($total_remates_fora / $total_remates) * 100 : 50;
                
                $total_remates_enq = $remates_enq_casa + $remates_enq_fora;
                $perc_enq_casa = $total_remates_enq > 0 ? ($remates_enq_casa / $total_remates_enq) * 100 : 50;
                $perc_enq_fora = $total_remates_enq > 0 ? ($remates_enq_fora / $total_remates_enq) * 100 : 50;
                
                $total_faltas = $faltas_casa + $faltas_fora;
                $perc_faltas_casa = $total_faltas > 0 ? ($faltas_casa / $total_faltas) * 100 : 50;
                $perc_faltas_fora = $total_faltas > 0 ? ($faltas_fora / $total_faltas) * 100 : 50;
                
                // Determinar qual equipa tem melhor resultado em cada estatística
                $classe_enq_casa = $remates_enq_casa >= $remates_enq_fora ? 'stat-melhor' : 'stat-pior';
                $classe_enq_fora = $remates_enq_fora > $remates_enq_casa ? 'stat-melhor' : 'stat-pior';
                
                $classe_remates_casa = $total_remates_casa >= $total_remates_fora ? 'stat-melhor' : 'stat-pior';
                $classe_remates_fora = $total_remates_fora > $total_remates_casa ? 'stat-melhor' : 'stat-pior';
                
                $classe_faltas_casa = $faltas_casa <= $faltas_fora ? 'stat-melhor' : 'stat-pior';
                $classe_faltas_fora = $faltas_fora < $faltas_casa ? 'stat-melhor' : 'stat-pior';
            ?>
            
            <!-- Estatísticas do jogo em destaque -->
            <div class="score-display">
              <img src="/vivedistrital/<?= htmlspecialchars($jogo_destaque['clube_casa_logo']) ?>" alt="<?= htmlspecialchars($jogo_destaque['clube_casa_nome']) ?>">
              <span class="resultado"><?= ($jogo_destaque['resultado_casa'] ?? '?') ?> - <?= ($jogo_destaque['resultado_fora'] ?? '?') ?></span>
              <img src="/vivedistrital/<?= htmlspecialchars($jogo_destaque['clube_fora_logo']) ?>" alt="<?= htmlspecialchars($jogo_destaque['clube_fora_nome']) ?>">
            </div>

            <div class="mb-3">
              <small class="estat-label">Remates enquadrados</small>
              <div class="stat-bar">
                <div class="stat-item <?= $classe_enq_casa ?>" style="width: <?= $perc_enq_casa ?>%"><?= $remates_enq_casa ?></div>
                <div class="stat-item <?= $classe_enq_fora ?>" style="width: <?= $perc_enq_fora ?>%"><?= $remates_enq_fora ?></div>
              </div>
            </div>

            <div class="mb-3">
              <small class="estat-label">Remates</small>
              <div class="stat-bar">
                <div class="stat-item <?= $classe_remates_casa ?>" style="width: <?= $perc_remates_casa ?>%"><?= $total_remates_casa ?></div>
                <div class="stat-item <?= $classe_remates_fora ?>" style="width: <?= $perc_remates_fora ?>%"><?= $total_remates_fora ?></div>
              </div>
            </div>

            <div class="mb-3">
              <small class="estat-label">Faltas</small>
              <div class="stat-bar">
                <div class="stat-item <?= $classe_faltas_casa ?>" style="width: <?= $perc_faltas_casa ?>%"><?= $faltas_casa ?></div>
                <div class="stat-item <?= $classe_faltas_fora ?>" style="width: <?= $perc_faltas_fora ?>%"><?= $faltas_fora ?></div>
              </div>
            </div>
            
            <?php else: ?>
              <!-- Mensagem quando não há jogo em destaque -->
              <div style="text-align: center; padding: 2rem; color: #888;">
                <i class="fas fa-star" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p style="margin: 0;">Sem jogo em destaque</p>
                <small>Defina um jogo como destaque no painel de administração</small>
              </div>
            <?php endif; ?>
            
            <?php
            // Tratamento de erros na consulta
            } catch (PDOException $e) {
              echo '<div style="text-align: center; padding: 2rem; color: #e74c3c;"><p>Erro ao carregar jogo em destaque</p></div>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>

  <!-- Jogos Principais: Recentes e Próximos -->
  <!-- Jogos Principais -->
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><span class="icone">sports_soccer</span>Jogos Principais</h5>
      <ul class="nav nav-tabs mb-0 mt-3" id="jogosTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="recentes-tab" data-bs-toggle="tab" data-bs-target="#recentes" type="button" role="tab" aria-controls="recentes" aria-selected="true">Jogos Recentes</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="proximos-tab" data-bs-toggle="tab" data-bs-target="#proximos" type="button" role="tab" aria-controls="proximos" aria-selected="false">Próximos Jogos</button>
        </li>
      </ul>
    </div>

    <div class="tab-content">
      <!-- Jogos Recentes -->
      <div class="tab-pane fade show active" id="recentes" role="tabpanel" aria-labelledby="recentes-tab">
        <div class="table-responsive">
          <table class="table table-dark table-hover">
            <thead>
              <tr>
                <th>Equipa A</th>
                <th>Resultado</th>
                <th>Equipa B</th>
                <th>Jornada</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Buscar jogos recentes finalizados
              // inner join para obter nomes e logos dos clubes
              // = finalizado para garantir que são passados
              // DESC para ordem cronológica dos mais recentes primeiros
              try {
                $stmt = $pdo->prepare("
                  SELECT j.*, 
                         cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
                         cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
                  FROM jogos j
                  INNER JOIN clubes cc ON j.clube_casa_id = cc.id
                  INNER JOIN clubes cf ON j.clube_fora_id = cf.id
                  WHERE j.status = 'finalizado'
                  ORDER BY j.data_jogo DESC
                  LIMIT 5
                ");
                $stmt->execute();
                $jogos_recentes = $stmt->fetchAll();
                
                // Exibir jogos recentes
                if (count($jogos_recentes) > 0):
                  // Loop pelos jogos recentes
                  foreach ($jogos_recentes as $jogo):
                    $data_formatada = date('d F Y', strtotime($jogo['data_jogo']));
                    $meses_pt = [
                      'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
                      'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
                      'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
                      'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
                    ];
                    $data_formatada = str_replace(array_keys($meses_pt), array_values($meses_pt), $data_formatada);
                ?>
                  <tr>
                    <td><a href="/vivedistrital/clube-detalhe.php?id=<?php echo $jogo['clube_casa_codigo']; ?>" class="equipa-row"><img src="/vivedistrital/<?php echo $jogo['clube_casa_logo']; ?>"><span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span></a></td>
                    <td><?php echo ($jogo['resultado_casa'] ?? 0) . '‑' . ($jogo['resultado_fora'] ?? 0); ?></td>
                    <td><a href="/vivedistrital/clube-detalhe.php?id=<?php echo $jogo['clube_fora_codigo']; ?>" class="equipa-row"><img src="/vivedistrital/<?php echo $jogo['clube_fora_logo']; ?>"><span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span></a></td>
                    <td><?php echo $jogo['jornada']; ?>º Jornada</td>
                    <td><?php echo $data_formatada; ?></td>
                  </tr>
                <?php
                  endforeach;
                else:
                ?>
                  <tr>
                    <td colspan="5" class="text-center">Nenhum jogo finalizado ainda</td>
                  </tr>
                <?php
                endif;
              } catch (PDOException $e) {
                echo '<tr><td colspan="5" class="text-center">Erro ao carregar jogos</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Próximos Jogos -->
      <div class="tab-pane fade" id="proximos" role="tabpanel" aria-labelledby="proximos-tab">
        <div class="table-responsive">
          <table class="table table-dark table-hover">
            <thead>
              <tr>
                <th>Equipa A</th>
                <th>Resultado</th>
                <th>Equipa B</th>
                <th>Jornada</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Buscar próximos jogos agendados
              try {
                // inner join para obter nomes e logos dos clubes
                // >= NOW() para garantir que são futuros
                // ASC para ordem cronológica dos mais próximos
                $stmt = $pdo->prepare("
                  SELECT j.*, 
                         cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
                         cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
                  FROM jogos j
                  INNER JOIN clubes cc ON j.clube_casa_id = cc.id
                  INNER JOIN clubes cf ON j.clube_fora_id = cf.id
                  WHERE j.status = 'agendado' AND j.data_jogo >= NOW()
                  ORDER BY j.data_jogo ASC
                  LIMIT 5
                ");
                $stmt->execute();
                // "Busca" todos os resultados
                $jogos_proximos = $stmt->fetchAll();
                
                if (count($jogos_proximos) > 0):
                  foreach ($jogos_proximos as $jogo):
                    $data_formatada = date('d F Y', strtotime($jogo['data_jogo']));
                    $meses_pt = [
                      'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
                      'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
                      'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
                      'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
                    ];
                    $data_formatada = str_replace(array_keys($meses_pt), array_values($meses_pt), $data_formatada);
                ?>
                  <tr>
                    <td><a href="/vivedistrital/clube-detalhe.php?id=<?php echo $jogo['clube_casa_codigo']; ?>" class="equipa-row"><img src="/vivedistrital/<?php echo $jogo['clube_casa_logo']; ?>"><span><?php echo htmlspecialchars($jogo['clube_casa_nome']); ?></span></a></td>
                    <td>–</td>
                    <td><a href="/vivedistrital/clube-detalhe.php?id=<?php echo $jogo['clube_fora_codigo']; ?>" class="equipa-row"><img src="/vivedistrital/<?php echo $jogo['clube_fora_logo']; ?>"><span><?php echo htmlspecialchars($jogo['clube_fora_nome']); ?></span></a></td>
                    <td><?php echo $jogo['jornada']; ?>ª Jornada</td>
                    <td><?php echo $data_formatada; ?></td>
                  </tr>
                <?php
                  endforeach;
                else:
                ?>
                  <tr>
                    <td colspan="5" class="text-center">Nenhum jogo agendado</td>
                  </tr>
                <?php
                endif;
              } catch (PDOException $e) {
                echo '<tr><td colspan="5" class="text-center">Erro ao carregar jogos</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Classificação -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <!-- emoji events é do Material Icons -->
      <h5 class="card-title mb-0"><span class="icone">emoji_events</span>Classificação</h5>
      <a href="/vivedistrital/classificacoes.php" class="btn-ver-mais">Ver mais</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark tabela-classificacao mb-0">
          <thead>
            <tr>
              <th style="width: 60px;">Pos</th>
              <th>Equipa</th>
              <th>J</th>
              <th>V</th>
              <th>E</th>
              <th>D</th>
              <th>+/-</th>
              <th>Pts</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            require_once __DIR__ . '/config-clubes.php';
            // Obter os 6 primeiros clubes da classificação
            // array_slice (função para limitar a quantidade de elementos) para limitar aos 6 primeiros
            $clubes_top6 = array_slice(getClubes(), 0, 6);
            // posição inicial
            $posicao = 1;
            foreach ($clubes_top6 as $clube): 
              // Calcular diferença de golos (?? = 0 para evitar erros se valores nulos)
              $golos_m = $clube['golos_marcados'] ?? 0;
              $golos_s = $clube['golos_sofridos'] ?? 0;
              $diferenca = $golos_m - $golos_s;
              $diferenca_texto = ($diferenca > 0 ? '+' : '') . $diferenca;
            ?>
            <tr data-clube-id="<?= $clube['id'] ?>">
              <td><?= $posicao ?></td>
              <td style="position: relative;">
                <a href="/vivedistrital/clube-detalhe.php?id=<?= e($clube['codigo']) ?>" class="equipa-row">
                  <!-- garante que aparece pelo menos uma imagem (default.png) -->
                  <img src="/vivedistrital/<?= e($clube['logo']) ?>" onerror="this.src='/vivedistrital/imgs/equipas/default.png'">
                  <span><?= e($clube['nome']) ?></span>
                </a>
                
                <!-- Tooltip forma recente (carregado dinamicamente) -->
                <div class="forma-recente-tooltip" data-clube-id="<?= $clube['id'] ?>">
                  <div class="forma-titulo">Últimos 5 Jogos</div>
                  <div class="forma-jogos">
                    <!-- spinner-border para indicar carregamento -->
                    <div class="spinner-border spinner-border-sm text-warning" role="status">
                      <span class="visually-hidden">A carregar...</span>
                    </div>
                  </div>
                </div>
              </td>
              <!-- Estatísticas do clube -->
              <!-- ?? = 0 para evitar erros se valores nulos -->
              <!-- Mostrar diferença de golos com sinal + ou - -->
              <!-- Pts em negrito -->
              <td><?= $clube['jogos'] ?? 0 ?></td>
              <td><?= $clube['vitorias'] ?? 0 ?></td>
              <td><?= $clube['empates'] ?? 0 ?></td>
              <td><?= $clube['derrotas'] ?? 0 ?></td>
              <td><?= $diferenca_texto ?></td>
              <td><strong><?= $clube['pontos'] ?? 0 ?></strong></td>
            </tr>
            <?php
            // Adicionar posição para o próximo clube para exibição 
              $posicao++;
            endforeach; 
            ?>
          </tbody>
        </table>
      </div>

      <!-- Legenda -->
      <div class="legenda-classificacao">
        <div class="legenda-item"><span class="legenda-marker ouro"></span><small>Campeão e Subida Divisão</small></div>
        <div class="legenda-item"><span class="legenda-marker prata"></span><small>Qualificação Taça Portugal</small></div>
      </div>
    </div>
  </div>
</div>

<script>
/**
 * Banner "Carousel" - Rotação automática de banners
 * Alterna entre 3 imagens a cada 5 segundos com transição suave
 */
(function() {
  // Array com os caminhos absolutos das imagens do banner
  const bannerImages = [
    '/vivedistrital/imgs/logos/banner-ecra-principal.png',
    '/vivedistrital/imgs/logos/banner-ecra-principal-2.png',
    '/vivedistrital/imgs/logos/banner-ecra-principal-3.png'
  ];
  
  // currentIndex guarda o índice da imagem atualmente exibida
  //autoplayInterval guarda o ID do setInterval para controle (iniciar/parar)
  // Inicialmente começa na primeira imagem (índice 0)
  // bannerImage é o elemento <img> do banner e bannerButton2/3 são os botões "clicáveis"
  let currentIndex = 0;
  let autoplayInterval = null;
  const bannerElement = document.getElementById('bannerImage');
  const button2 = document.getElementById('bannerButton2');
  const button3 = document.getElementById('bannerButton3');
  
  // Configurar transição CSS no elemento
  if (bannerElement) {
    bannerElement.style.transition = 'opacity 0.3s ease-in-out';
  }
  
  /**
   * Altera o banner para o índice especificado com transição fade
   * @param {number} index - Índice da imagem no array
   */
  function setBanner(index) {
    // Garantir que o índice está dentro dos limites (circular)
    if (index >= bannerImages.length) {
      currentIndex = 0;
    } else if (index < 0) {
      // -1 para voltar ao último banner
      currentIndex = bannerImages.length - 1;
    } else {
      currentIndex = index;
    }
    
    // Mostrar/ocultar botões clicáveis conforme o banner ativo
    // Banner 1 (índice 0): sem botão
    // Banner 2 (índice 1): botão "Criar Conta Grátis"
    // Banner 3 (índice 2): botão "Ver Notícias"
    button2.style.display = (currentIndex === 1) ? 'block' : 'none'; 
    button3.style.display = (currentIndex === 2) ? 'block' : 'none'; // block para mostrar, none para ocultar
    
    // Aplicar transição fade suave
    bannerElement.style.opacity = '0';
    
    // setTimeout para esperar a transição de opacidade antes de trocar a imagem
    setTimeout(() => {
      bannerElement.src = bannerImages[currentIndex];
      bannerElement.style.opacity = '1';
    }, 300); // 300ms para coincidir com a duração da transição CSS
  }
  
  /**
   * Avança ou recua no carrossel
   * @param {number} direction - 1 para avançar, -1 para recuar
   */
  window.changeBanner = function(direction) {
    setBanner(currentIndex + direction);
    resetAutoplay();
  };
  
  /**
   * Inicia a rotação automática (5 segundos)
   */
  function startAutoplay() {
    autoplayInterval = setInterval(() => {
      setBanner(currentIndex + 1);
    }, 5000); // 5000ms = 5 segundos
  }
  
  /**
   * Reinicia o timer do autoplay (chamado ao clicar nas setas)
   */
  function resetAutoplay() {
    if (autoplayInterval) {
      clearInterval(autoplayInterval);
    }
    startAutoplay();
  }
  
  // Iniciar autoplay ao carregar
  startAutoplay();
})();

/**
 * Sistema de Forma Recente - Classificação
 * Carrega os últimos 5 jogos de cada clube ao fazer "hover" / cursor sobre o nome do clube
 */
(function() {
  const formaCache = new Map(); // Cache para evitar requests repetidos
  
  /**
   * param para carregar forma recente via AJAX
   * AJAX é um método para atualizar partes de uma página web sem recarregar a página inteira
   * Carregar forma recente de um clube
   * @param {number} clubeId - ID do clube
   * @returns {Promise<Array>} Array com últimos 5 jogos (V/E/D)
   * promise é um objeto que representa a eventual conclusão (ou falha) de uma operação assíncrona
   */
  async function carregarFormaRecente(clubeId) {
    // Verificar cache
    if (formaCache.has(clubeId)) {
      return formaCache.get(clubeId);
    }
    
    try {
      // vai buscar a API para obter a forma recente
      const response = await fetch(`/vivedistrital/api/forma-recente.php?clube_id=${clubeId}`);
      
      if (!response.ok) {
        throw new Error('Erro ao carregar forma recente');
      }
      
      // await response.json() para converter a resposta em JSON
      // JSON (JavaScript Object Notation) é um formato de troca de dados
      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Erro desconhecido');
      }
      
      // Armazenar em cache
      // Cache é uma técnica para armazenar dados temporariamente para acesso rápido
      formaCache.set(clubeId, data.forma);
      return data.forma;
      
    } catch (error) {
      console.error('Erro ao carregar forma recente:', error);
      // Fallback/Garantia: retornar array vazio
      return [null, null, null, null, null];
    }
  }
  
  /**
   * Renderizar forma recente no tooltip
   * @param {HTMLElement} tooltip - Elemento do tooltip, HTMLElement é um objeto que representa um elemento HTML na página
   * @param {Array} forma - Array com V/E/D
   */
  function renderizarForma(tooltip, forma) { // tooltip é o elemento onde os resultados serão exibidos
    const container = tooltip.querySelector('.forma-jogos'); // forma-jogos é o container onde os resultados serão exibidos
    
    if (!forma || forma.length === 0) { // forma.length === 0 significa que não há dados
      container.innerHTML = '<small style="color: #888;">Sem dados</small>';
      return;
    }
    
    // Limpar spinner
    container.innerHTML = '';
    
    // Renderizar/Apresentar cada resultado (já vem na ordem correta da API)
    forma.forEach(resultado => {
      const span = document.createElement('div'); // div para cada resultado
      span.className = 'forma-jogo';
      
      if (resultado === null) {
        // Jogo não disponível
        span.style.backgroundColor = '#2a2a2a';
        span.style.border = '1px dashed #444';
        span.textContent = '—';
        span.title = 'Jogo não disponível';
      } else {
        switch (resultado) {
          case 'V':
            span.classList.add('vitoria');
            span.textContent = 'V';
            span.title = 'Vitória';
            break;
          case 'E':
            span.classList.add('empate');
            span.textContent = 'E';
            span.title = 'Empate';
            break;
          case 'D':
            span.classList.add('derrota');
            span.textContent = 'D';
            span.title = 'Derrota';
            break;
        }
      }
      
      container.appendChild(span);
    });
  }
  
  /**
   * Inicializar tooltips de forma recente
   */
  function inicializarFormaRecente() {
    const linhasClassificacao = document.querySelectorAll('.tabela-classificacao tbody tr[data-clube-id]'); //querySelectorAll seleciona todos os elementos
    
    linhasClassificacao.forEach(linha => {
      const clubeId = parseInt(linha.dataset.clubeId); // linha.dataset.clubeId obtém o ID do clube do atributo data-clube-id
      const tooltip = linha.querySelector('.forma-recente-tooltip'); // forma-recente-tooltip é o elemento do tooltip
      
      if (!tooltip) return;
      
      // Carregar forma ao fazer hover (com debounce (que evita múltiplos requests rápidos))
      let timeoutId = null;
      
      linha.addEventListener('mouseenter', async () => { // mouse entra
        // Evitar carregar se já tem dados
        const container = tooltip.querySelector('.forma-jogos');
        if (!container.querySelector('.spinner-border')) {
          return; // Já foi carregado
        }
        
        // Carregar dados
        const forma = await carregarFormaRecente(clubeId);
        renderizarForma(tooltip, forma);
      });
    });
  }
  
  // Inicializar quando DOM (Document Object Model um objeto que representa a estrutura do documento HTML) estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarFormaRecente);
  } else {
    inicializarFormaRecente();
  }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
