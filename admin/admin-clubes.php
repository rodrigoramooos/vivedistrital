<?php

// Configurar encoding UTF-8 (mais utilizado para páginas HTML)
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config-clubes.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Processar mensagens de feedback
$mensagem = '';
$tipo_mensagem = '';

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'success';
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}

// Obter todos os clubes
$clubes = getClubes();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestão de Clubes | Vive Distrital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/vivedistrital/css/comum.css">
    <link rel="stylesheet" href="/vivedistrital/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <a href="/vivedistrital/admin/admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-chart-bar"></i> Estatísticas dos Clubes</h1>
                    <p style="color: #999; margin: 10px 0 0 0;">
                        <i class="fas fa-info-circle"></i> Estatísticas calculadas automaticamente a partir dos jogos registados
                    </p>
                </div>
                <a href="/vivedistrital/api/recalcular-estatisticas.php" 
                   class="btn btn-dark"
                   onclick="return confirm('Recalcular todas as estatísticas a partir dos jogos?');"
                   style="background: #1a1a1a; padding: 12px 24px; border-radius: 8px; color: #fff; text-decoration: none; border: 2px solid #333;">
                    <i class="fas fa-sync-alt"></i> Recalcular Tudo
                </a>
            </div>
        </div>

        <div class="alert alert-warning" style="margin: 20px 0; background: #ff9800; border-color: #ff9800; color: #000;">
            <i class="fas fa-lock"></i> <strong>Modo Somente Leitura:</strong> 
            As estatísticas são calculadas dinamicamente. Para alterar, vá para 
            <a href="/vivedistrital/admin/admin-jogos.php" style="color: #000; text-decoration: underline; font-weight: bold;">Gerir Jogos</a>.
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                <?= e($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php foreach ($clubes as $clube): ?>
            <div class="clube-card" style="border: 2px solid #ffc107; background: #2a2a2a; border-radius: 12px; margin-bottom: 20px; overflow: hidden;">
                <div class="clube-header" style="background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); padding: 20px; border-bottom: 2px solid #ffc107;">
                    <img src="<?= e($clube['logo']) ?>" alt="<?= e($clube['nome']) ?>" onerror="this.src='../imgs/equipas/<?= strtolower($clube['codigo']) ?>.png'" style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px;">
                    <div>
                        <h3 style="color: #ffc107; margin: 0; font-size: 1.5rem;"><?= e($clube['nome']) ?></h3>
                        <small style="color: #999;">Código: <?= e($clube['codigo']) ?></small>
                    </div>
                </div>

                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; padding: 20px;">
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #999; font-size: 12px; margin-bottom: 5px;">JOGOS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['jogos'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #999; font-size: 12px; margin-bottom: 5px;">PONTOS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['pontos'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #999; font-size: 12px; margin-bottom: 5px;">VITÓRIAS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['vitorias'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #fff; font-size: 12px; margin-bottom: 5px;">EMPATES</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['empates'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #fff; font-size: 12px; margin-bottom: 5px;">DERROTAS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['derrotas'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #fff; font-size: 12px; margin-bottom: 5px;">GOLOS MARCADOS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['golos_marcados'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #999; font-size: 12px; margin-bottom: 5px;">GOLOS SOFRIDOS</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= $clube['golos_sofridos'] ?? 0 ?></div>
                    </div>
                    <div class="stat-box" style="background: #1a1a1a; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #333;">
                        <div style="color: #999; font-size: 12px; margin-bottom: 5px;">DIFERENÇA</div>
                        <div style="color: #fff; font-size: 24px; font-weight: bold;"><?= ($clube['diferenca_golos'] ?? 0) >= 0 ? '+' : '' ?><?= $clube['diferenca_golos'] ?? 0 ?></div>
                    </div>
                </div>

                <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #333; background: #1a1a1a;">
                    <div>
                        <strong style="color: #ffc107; font-size: 12px;">FORMA:</strong> 
                        <?= formatarForma($clube['forma'] ?? '') ?>
                    </div>
                    <span style="color: #999; font-size: 12px;">
                        <i class="fas fa-trophy" style="color: #ffc107;"></i> Posição: <strong style="color: #fff;"><?= $clube['posicao'] ?? '-' ?>º</strong>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
