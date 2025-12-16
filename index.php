<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/config.php';

// Configurações da página
$pageTitle = 'Página Inicial';
$pageCSS = 'css/pagina-inicial.css';
$additionalStyles = "
  .jogo-destaque {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .jogo-destaque .card {
    height: 100%;
  }

  .jogo-destaque .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .score-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .score-display img {
    width: 50px;
    height: 50px;
    object-fit: contain;
  }

  .resultado {
    background-color: #333;
    padding: 0.3rem 1rem;
    border-radius: 12px;
    font-weight: bold;
  }

  .stat-bar {
    display: flex;
    height: 22px;
    border-radius: 10px;
    overflow: hidden;
    background-color: #2c2c2c;
    margin-bottom: 0.75rem;
  }

  .stat-item {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
  }

  .stat-amarelo {
    background-color: var(--primary-accent);
    color: #000;
  }

  .stat-azul {
    background-color: #1e6aff;
    color: #fff;
  }

  .table-dark {
    color: #FFFFFF;
  }

  .table-dark thead th {
    color: #888888;
    border-bottom: 1px solid #333;
    font-size: 0.8rem;
  }

  .table-dark tbody tr {
    background-color: transparent;
  }

  .table-dark tbody td {
    color: #FFFFFF;
    border-bottom: 1px solid #2b2b2b;
    font-size: 0.9rem;
  }

  .table-dark tbody tr:hover {
    background-color: #222;
  }

  .tabela-classificacao.table-dark tbody tr:nth-child(1) {
    background-color: rgba(255, 215, 0, 0.1);
    border-left: 4px solid #FFD700;
  }

  .tabela-classificacao.table-dark tbody tr:nth-child(2) {
    background-color: rgba(82, 187, 56, 0.12);
    border-left: 4px solid #8ca044;
  }

  .posicao {
    font-weight: bold;
    width: 60px;
    text-align: left;
    padding-left: 0.5rem;
  }

  .equipa-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.2s;
    text-decoration: none;
    color: inherit;
  }

  .equipa-row:hover {
    opacity: 0.8;
    transform: scale(1.02);
  }

  .equipa-row img {
    width: 28px;
    height: 28px;
    object-fit: contain;
  }

  .legenda-classificacao {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .legenda-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #CCCCCC;
    font-size: 0.9rem;
  }

  .legenda-marker {
    width: 14px;
    height: 14px;
    display: inline-block;
    border-radius: 3px;
    border: 1px solid rgba(0,0,0,.2);
  }

  .legenda-marker.ouro { background: #FFD700; }
  .legenda-marker.prata { background: #8ca044; }

  .nav-tabs {
    border-bottom: 1px solid #333;
  }

  .nav-tabs .nav-link {
    color: #CCCCCC;
    border: none;
    border-bottom: 2px solid transparent;
  }

  .nav-tabs .nav-link.active {
    background-color: transparent;
    color: var(--primary-accent);
    border-bottom-color: var(--primary-accent);
  }

  .nav-tabs .nav-link:hover {
    color: #FFFFFF;
    border-bottom-color: transparent;
  }

  .btn-ver-mais {
    background: none;
    border: 1px solid rgba(241,196,15,0.15);
    color: var(--primary-accent);
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: background 0.15s, color 0.15s;
    text-decoration: none;
  }

  .btn-ver-mais:hover {
    background-color: rgba(241,196,15,0.08);
    color: #fff;
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
    .search-box {
      width: 100%;
    }
    .jogo-destaque {
      width: 100%;
      margin-top: 1rem;
    }
  }
";

include 'includes/header.php';
include 'includes/sidebar.php';
?>

  <div class="main-content">

    <?php include 'includes/topbar.php'; ?>

    <!-- Banner + Jogo em Destaque -->
    <div class="row mb-4 g-3">
      <div class="col-lg-8">
        <img src="<?php echo url('imgs/logos/banner-ecra-principal.png'); ?>" alt="Banner" class="img-fluid rounded" style="width: 100%;">
      </div>
      <div class="col-lg-4">
        <div class="card jogo-destaque">
          <div class="card-body">
            <h5 class="card-title">Jogo em Destaque</h5>
            
            <div class="score-display">
              <img src="<?php echo url('imgs/equipas/poiares.png'); ?>" alt="AD Poiares">
              <span class="resultado">2 - 4</span>
              <img src="<?php echo url('imgs/equipas/aac-sf.png'); ?>" alt="Académica SF">
            </div>

            <div class="mb-3">
              <small class="estat-label">Remates enquadrados</small>
              <div class="stat-bar">
                <div class="stat-item stat-amarelo" style="width: 60%">4</div>
                <div class="stat-item stat-azul" style="width: 40%">3</div>
              </div>
            </div>

            <div class="mb-3">
              <small class="estat-label">Remates</small>
              <div class="stat-bar">
                <div class="stat-item stat-amarelo" style="width: 70%">12</div>
                <div class="stat-item stat-azul" style="width: 35%">6</div>
              </div>
            </div>

            <div class="mb-3">
              <small class="estat-label">Faltas</small>
              <div class="stat-bar">
                <div class="stat-item stat-amarelo" style="width: 90%">21</div>
                <div class="stat-item stat-azul" style="width: 60%">14</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=sourense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/sourense.png'); ?>"><span>Sourense</span></a></td>
                <td>5‑1</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=pedrulhense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/pedrulhense.png'); ?>"><span>Pedrulhense</span></a></td>
                <td>5º Jornada</td>
                <td>9 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=poiares'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/poiares.png'); ?>"><span>Poiares AD</span></a></td>
                <td>2‑1</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=uniao'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/uniao.png'); ?>"><span>União FC</span></a></td>
                <td>5º Jornada</td>
                <td>9 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=esperanca'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/esperanca.png'); ?>"><span>Esperança AC</span></a></td>
                <td>1‑3</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=tourizense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/tourizense.png'); ?>"><span>Tourizense</span></a></td>
                <td>5º Jornada</td>
                <td>9 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=anca'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/anca.png'); ?>"><span>Ançã</span></a></td>
                <td>1‑0</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=academica'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/aac-sf.png'); ?>"><span>Académica SF</span></a></td>
                <td>5º Jornada</td>
                <td>9 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=nogueirense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/nogueirense.png'); ?>"><span>Nogueirense</span></a></td>
                <td>0‑0</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=uniao1919'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/uniaocoimbra.png'); ?>"><span>União 1919</span></a></td>
                <td>5º Jornada</td>
                <td>9 Novembro 2025</td>
              </tr>
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
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=nogueirense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/nogueirense.png'); ?>"><span>Nogueirense</span></a></td>
                <td>–</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=academica'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/aac-sf.png'); ?>"><span>Académica SF</span></a></td>
                <td>8ª Jornada</td>
                <td>23 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=anca'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/anca.png'); ?>"><span>Ançã</span></a></td>
                <td>–</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=tocha'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/tocha.png'); ?>"><span>Tocha</span></a></td>
                <td>8ª Jornada</td>
                <td>23 Novembro 2025</td>
              </tr>
              <tr>
                <td><a href="<?php echo url('clube-detalhe.php?id=sourense'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/sourense.png'); ?>"><span>Sourense</span></a></td>
                <td>–</td>
                <td><a href="<?php echo url('clube-detalhe.php?id=uniao'); ?>" class="equipa-row"><img src="<?php echo url('imgs/equipas/uniao.png'); ?>"><span>União FC</span></a></td>
                <td>8ª Jornada</td>
                <td>23 Novembro 2025</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Classificação -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0"><span class="icone">emoji_events</span>Classificação</h5>
      <a href="<?php echo url('classificacoes.php'); ?>" class="btn-ver-mais">Ver mais</a>
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
            require_once 'config-clubes.php';
            $clubes_top6 = array_slice(getClubes(), 0, 6);
            $posicao = 1;
            foreach ($clubes_top6 as $clube): 
              $golos_m = $clube['golos_marcados'] ?? 0;
              $golos_s = $clube['golos_sofridos'] ?? 0;
              $diferenca = $golos_m - $golos_s;
              $diferenca_texto = ($diferenca > 0 ? '+' : '') . $diferenca;
            ?>
            <tr>
              <td><?= $posicao ?></td>
              <td>
                <a href="<?= url('clube-detalhe.php?id=' . e($clube['codigo'])) ?>" class="equipa-row">
                  <img src="<?= url(e($clube['logo'])) ?>" onerror="this.src='<?= url('imgs/equipas/default.png') ?>'">
                  <span><?= e($clube['nome']) ?></span>
                </a>
              </td>
              <td><?= $clube['jogos'] ?? 0 ?></td>
              <td><?= $clube['vitorias'] ?? 0 ?></td>
              <td><?= $clube['empates'] ?? 0 ?></td>
              <td><?= $clube['derrotas'] ?? 0 ?></td>
              <td><?= $diferenca_texto ?></td>
              <td><strong><?= $clube['pontos'] ?? 0 ?></strong></td>
            </tr>
            <?php 
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

<?php include 'includes/footer.php'; ?>
