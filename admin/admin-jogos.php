<?php
require_once __DIR__ . '/../config-clubes.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ' . url('login.php'));
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

$pdo = getDB();
$stmt = $pdo->query("SELECT j.*, cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo, cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo FROM jogos j JOIN clubes cc ON j.clube_casa_id = cc.id JOIN clubes cf ON j.clube_fora_id = cf.id ORDER BY j.data_jogo DESC");
$jogos = $stmt->fetchAll();

$clubes = getClubes();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestão de Jogos | Vive Distrital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('css/admin.css'); ?>">
</head>
<body>
    <div class="admin-container">
        <a href="<?= url('admin/admin.php') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-calendar-alt"></i> Gestão de Jogos</h1>
                </div>
                <a href="#add-new-jogo" class="btn-add-jogo" onclick="document.getElementById('novo-jogo-form').scrollIntoView({behavior: 'smooth'});">
                    <i class="fas fa-plus"></i> Adicionar Novo Jogo
                </a>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                <?= e($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div id="novo-jogo-form" class="jogo-card" style="border-color: #27ae60;">
            <form method="POST" action="<?= url('admin/admin-jogos-editar.php') ?>">
                <input type="hidden" name="action" value="add">
                
                <h3 style="color: #27ae60; margin-bottom: 20px;">
                    <i class="fas fa-plus-circle"></i> Novo Jogo
                </h3>

                <div class="jogo-detalhes">
                    <div class="form-group">
                        <label>Clube Casa</label>
                        <select name="clube_casa_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($clubes as $clube): ?>
                                <option value="<?= $clube['id'] ?>"><?= e($clube['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Clube Fora</label>
                        <select name="clube_fora_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($clubes as $clube): ?>
                                <option value="<?= $clube['id'] ?>"><?= e($clube['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Data e Hora</label>
                        <input type="datetime-local" name="data_jogo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Jornada</label>
                        <input type="number" name="jornada" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Resultado Casa</label>
                        <input type="number" name="resultado_casa" class="form-control" min="0">
                    </div>

                    <div class="form-group">
                        <label>Resultado Fora</label>
                        <input type="number" name="resultado_fora" class="form-control" min="0">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="agendado">Agendado</option>
                            <option value="finalizado">Finalizado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-plus"></i> Adicionar Jogo
                </button>
            </form>
        </div>

        <h3 style="color: #f1c40f; margin: 30px 0 20px 0;">
            <i class="fas fa-list"></i> Jogos Registados (<?= count($jogos) ?>)
        </h3>

        <?php foreach ($jogos as $jogo): ?>
            <form method="POST" action="<?= url('admin/admin-jogos-editar.php') ?>" class="jogo-card">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $jogo['id'] ?>">
                
                <div class="jogo-header">
                    <div class="jogo-info">
                        <?php if (!empty($jogo['clube_casa_logo']) && file_exists(__DIR__ . '/../' . $jogo['clube_casa_logo'])): ?>
                            <img src="<?= e(url($jogo['clube_casa_logo'])) ?>" alt="<?= e($jogo['clube_casa_nome']) ?>">
                        <?php endif; ?>
                        <strong><?= e($jogo['clube_casa_nome']) ?></strong>
                        <span class="versus">VS</span>
                        <strong><?= e($jogo['clube_fora_nome']) ?></strong>
                        <?php if (!empty($jogo['clube_fora_logo']) && file_exists(__DIR__ . '/../' . $jogo['clube_fora_logo'])): ?>
                            <img src="<?= e(url($jogo['clube_fora_logo'])) ?>" alt="<?= e($jogo['clube_fora_nome']) ?>">
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="status-badge status-<?= $jogo['status'] ?>">
                            <?= ucfirst($jogo['status']) ?>
                        </span>
                        <button type="button" class="btn-delete" onclick="if(confirm('Tem certeza que deseja eliminar este jogo?')) { this.form.action='<?= url('admin/admin-jogos-editar.php?action=delete&id=' . $jogo['id']) ?>'; this.form.submit(); }">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="jogo-detalhes">
                    <div class="form-group">
                        <label>Data e Hora</label>
                        <input type="datetime-local" name="data_jogo" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($jogo['data_jogo'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jornada</label>
                        <input type="number" name="jornada" class="form-control" value="<?= $jogo['jornada'] ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Resultado Casa</label>
                        <input type="number" name="resultado_casa" class="form-control" value="<?= $jogo['resultado_casa'] ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Resultado Fora</label>
                        <input type="number" name="resultado_fora" class="form-control" value="<?= $jogo['resultado_fora'] ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="agendado" <?= $jogo['status'] == 'agendado' ? 'selected' : '' ?>>Agendado</option>
                            <option value="finalizado" <?= $jogo['status'] == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            <option value="cancelado" <?= $jogo['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Guardar Alterações
                </button>
            </form>
        <?php endforeach; ?>

        <?php if (empty($jogos)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 20px;"></i>
                <p>Nenhum jogo registado. Adicione o primeiro jogo acima!</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
