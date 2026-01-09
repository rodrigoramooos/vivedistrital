<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/config.php';
require_once 'config-clubes.php';

$pageTitle = 'Classificações';
$pageCSS = 'css/classificacoes.css';

$clubes = getClubes();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
  <?php include 'includes/topbar.php'; ?>

  <div class="card">
    <div class="card-header">
      <h5 class="card-title"><span class="icone">emoji_events</span>Classificação - Campeonato Distrital</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark mb-0">
          <thead>
            <tr>
              <th style="width: 60px;">Pos</th>
              <th>Equipa</th>
              <th>J</th>
              <th>V</th>
              <th>E</th>
              <th>D</th>
              <th>GM</th>
              <th>GS</th>
              <th>DG</th>
              <th>Pts</th>
              <th>Forma</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $posicao = 1;
            foreach ($clubes as $clube): 
              $golos_m = $clube['golos_marcados'] ?? 0;
              $golos_s = $clube['golos_sofridos'] ?? 0;
              $diferenca = $golos_m - $golos_s;
              $diferenca_texto = ($diferenca > 0 ? '+' : '') . $diferenca;
            ?>
            <tr>
              <td><?= $posicao ?>º</td>
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
              <td><?= $golos_m ?></td>
              <td><?= $golos_s ?></td>
              <td><?= $diferenca_texto ?></td>
              <td><strong><?= $clube['pontos'] ?? 0 ?></strong></td>
              <td><?= formatarForma($clube['forma'] ?? '') ?></td>
            </tr>
            <?php 
              $posicao++;
            endforeach; 
            ?>
          </tbody>
        </table>
      </div>

      <div class="legenda-classificacao">
        <div class="legenda-item">
          <span class="legenda-cor ouro"></span>
          <span class="legenda-texto">Campeão / Subida Divisão</span>
        </div>
        <div class="legenda-item">
          <span class="legenda-cor prata"></span>
          <span class="legenda-texto">Qualificação Taça Portugal</span>
        </div>
        <div class="legenda-item">
          <span class="legenda-cor playoff"></span>
          <span class="legenda-texto">Playoff Despromoção</span>
        </div>
        <div class="legenda-item">
          <span class="legenda-cor descida"></span>
          <span class="legenda-texto">Descida Divisão</span>
        </div>
      </div>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
