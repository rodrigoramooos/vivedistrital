<?php
/**
 * PÁGINA DE CLASSIFICAÇÕES - VIVE DISTRITAL

 * Apresenta a tabela classificativa completa da Divisão Elite de Coimbra.
 * Todas as estatísticas são calculadas dinamicamente a partir dos jogos:
 * - Posição (ordenada por pontos, diferença de golos, vitórias)
 * - Jogos, Vitórias, Empates, Derrotas
 * - Golos Marcados, Golos Sofridos, Diferença de Golos
 * - Pontos (3 por vitória, 1 por empate)
 * - Forma (últimos 5 resultados: V-vitória, E-empate, D-derrota)
 */

// Configurar encoding UTF-8 (padrão de codificação mais utilizado na web)
header('Content-Type: text/html; charset=utf-8');

// Importar dependências
require_once __DIR__ . '/includes/config.php';    // Conexão BD
require_once __DIR__ . '/config-clubes.php';       // Funções auxiliares

$pageTitle = 'Classificações';
$pageCSS = '/vivedistrital/css/classificacoes.css';

// getClubes() retorna todos os clubes ordenados por:
// 1º - Pontos (DESC)
// 2º - Diferença de golos (DESC)
// 3º - Vitórias (DESC)
// Função definida em config-clubes.php
$clubes = getClubes();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
  <!-- Barra Superior -->
  <?php include __DIR__ . '/includes/topbar.php'; ?>

  <!-- Classificação Completa -->
  <div class="card">
    <div class="card-header">
      <h5 class="card-title"><span class="icone">emoji_events</span>Classificação - Divisão Elite de Coimbra</h5>
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
            foreach ($clubes as $clube): // clubes as $clube para iterar cada clube
              $golos_m = $clube['golos_marcados'] ?? 0;
              $golos_s = $clube['golos_sofridos'] ?? 0;
              $diferenca = $golos_m - $golos_s;
              $diferenca_texto = ($diferenca > 0 ? '+' : '') . $diferenca;
            ?>
            <tr>
              <td><?= $posicao ?>º</td>
              <td>
                <a href="/vivedistrital/clube-detalhe.php?id=<?= e($clube['codigo']) ?>" class="equipa-row">
                  <img src="/vivedistrital/<?= e($clube['logo']) ?>" onerror="this.src='/vivedistrital/imgs/equipas/default.png'">
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

      <!-- Legenda -->
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

<?php include __DIR__ . '/includes/footer.php'; ?>
