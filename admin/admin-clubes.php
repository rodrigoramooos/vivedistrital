<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config-clubes.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

$mensagem = '';
$tipo_mensagem = '';

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'success';
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}

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
    <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/admin.css'); ?>">
</head>
<body>
    <div class="admin-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <h1><i class="fas fa-users-cog"></i> Gestão de Clubes</h1>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                <?= e($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php foreach ($clubes as $clube): ?>
            <form method="POST" action="admin-clubes-editar.php" class="clube-card">
                <input type="hidden" name="id" value="<?= $clube['id'] ?>">
                
                <div class="clube-header">
                    <img src="<?= e($clube['logo']) ?>" alt="<?= e($clube['nome']) ?>" onerror="this.src='../imgs/equipas/<?= strtolower($clube['codigo']) ?>.png'">
                    <div>
                        <h3><?= e($clube['nome']) ?></h3>
                        <small style="color: #999;">Código: <?= e($clube['codigo']) ?></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Logo (URL/Caminho)</label>
                        <input type="text" class="form-control" name="logo" value="<?= e($clube['logo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jogos</label>
                        <input type="number" class="form-control" name="jogos" value="<?= $clube['jogos'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Pontos</label>
                        <input type="number" class="form-control" name="pontos" value="<?= $clube['pontos'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Vitórias</label>
                        <input type="number" class="form-control" name="vitorias" value="<?= $clube['vitorias'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Empates</label>
                        <input type="number" class="form-control" name="empates" value="<?= $clube['empates'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Derrotas</label>
                        <input type="number" class="form-control" name="derrotas" value="<?= $clube['derrotas'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Golos Marcados</label>
                        <input type="number" class="form-control" name="golos_marcados" value="<?= $clube['golos_marcados'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Golos Sofridos</label>
                        <input type="number" class="form-control" name="golos_sofridos" value="<?= $clube['golos_sofridos'] ?? 0 ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Forma</label>
                        <input type="text" class="form-control" name="forma" value="<?= e($clube['forma'] ?? '') ?>" placeholder="VVDVE" maxlength="50">
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Guardar Alterações
                </button>
            </form>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
