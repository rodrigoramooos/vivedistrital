<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/config.php';

$pageTitle = 'Página Inicial';
$pageCSS = 'css/pagina-inicial.css';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

  <div class="main-content">

    <?php include 'includes/topbar.php'; ?>

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

      <div class="legenda-classificacao">
        <div class="legenda-item"><span class="legenda-marker ouro"></span><small>Campeão e Subida Divisão</small></div>
        <div class="legenda-item"><span class="legenda-marker prata"></span><small>Qualificação Taça Portugal</small></div>
      </div>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
