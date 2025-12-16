<?php
require_once 'config-clubes.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

// Processar mensagens
$mensagem = '';
$tipo_mensagem = '';

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'success';
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}

// Obter todos os jogos ordenados por data
$pdo = getDB();
$stmt = $pdo->query("
    SELECT j.*, 
           cc.nome as clube_casa_nome, cc.logo as clube_casa_logo,
           cf.nome as clube_fora_nome, cf.logo as clube_fora_logo
    FROM jogos j
    JOIN clubes cc ON j.clube_casa_id = cc.id
    JOIN clubes cf ON j.clube_fora_id = cf.id
    ORDER BY j.data_jogo DESC
");
$jogos = $stmt->fetchAll();

// Obter todos os clubes para os formulários
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
    <link rel="stylesheet" href="css/comum.css">
    <style>
        body {
            background: #0D0D0D;
            color: #fff;
        }
        .admin-container {
            max-width: 1600px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, #1A1A1A 0%, #2D2D2D 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #333;
        }
        .admin-header h1 {
            color: #f1c40f;
            margin: 0;
            font-size: 2rem;
        }
        .btn-add-jogo {
            background: #27ae60;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-add-jogo:hover {
            background: #2ecc71;
            transform: translateY(-2px);
            color: #fff;
        }
        .jogo-card {
            background: #1A1A1A;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }
        .jogo-card:hover {
            border-color: #f1c40f;
            box-shadow: 0 0 20px rgba(241, 196, 15, 0.2);
        }
        .jogo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .jogo-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .jogo-info img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        .versus {
            color: #f1c40f;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .jogo-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #f1c40f;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            background: #0D0D0D;
            border: 1px solid #444;
            color: #fff;
            border-radius: 8px;
            padding: 10px;
        }
        .form-control:focus, .form-select:focus {
            background: #0D0D0D;
            border-color: #f1c40f;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
        }
        .form-select option {
            background: #1A1A1A;
            color: #fff;
        }
        .btn-save {
            background: #f1c40f;
            color: #0D0D0D;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            background: #ffd700;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(241, 196, 15, 0.4);
        }
        .btn-delete {
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-agendado {
            background: #3498db;
            color: #fff;
        }
        .status-realizado {
            background: #27ae60;
            color: #fff;
        }
        .status-cancelado {
            background: #e74c3c;
            color: #fff;
        }
        .back-link {
            display: inline-block;
            color: #f1c40f;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: #ffd700;
            transform: translateX(-5px);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .alert-success {
            background: #27ae60;
            color: #fff;
        }
        .alert-danger {
            background: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-calendar-alt"></i> Gestão de Jogos</h1>
                    <p style="color: #999; margin: 10px 0 0 0;">Adicione e edite os jogos da competição</p>
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

        <!-- Formulário para Adicionar Novo Jogo -->
        <div id="novo-jogo-form" class="jogo-card" style="border-color: #27ae60;">
            <form method="POST" action="admin-jogos-editar.php">
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
                        <input type="number" name="resultado_casa" class="form-control" min="0" placeholder="Deixe vazio se agendado">
                    </div>

                    <div class="form-group">
                        <label>Resultado Fora</label>
                        <input type="number" name="resultado_fora" class="form-control" min="0" placeholder="Deixe vazio se agendado">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="agendado">Agendado</option>
                            <option value="realizado">Realizado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-plus"></i> Adicionar Jogo
                </button>
            </form>
        </div>

        <!-- Lista de Jogos Existentes -->
        <h3 style="color: #f1c40f; margin: 30px 0 20px 0;">
            <i class="fas fa-list"></i> Jogos Registados (<?= count($jogos) ?>)
        </h3>

        <?php foreach ($jogos as $jogo): ?>
            <form method="POST" action="admin-jogos-editar.php" class="jogo-card">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $jogo['id'] ?>">
                
                <div class="jogo-header">
                    <div class="jogo-info">
                        <img src="<?= e($jogo['clube_casa_logo']) ?>" alt="<?= e($jogo['clube_casa_nome']) ?>">
                        <strong><?= e($jogo['clube_casa_nome']) ?></strong>
                        <span class="versus">VS</span>
                        <strong><?= e($jogo['clube_fora_nome']) ?></strong>
                        <img src="<?= e($jogo['clube_fora_logo']) ?>" alt="<?= e($jogo['clube_fora_nome']) ?>">
                    </div>
                    <div>
                        <span class="status-badge status-<?= $jogo['status'] ?>">
                            <?= ucfirst($jogo['status']) ?>
                        </span>
                        <button type="button" class="btn-delete" onclick="if(confirm('Tem certeza que deseja eliminar este jogo?')) { this.form.action='admin-jogos-editar.php?action=delete&id=<?= $jogo['id'] ?>'; this.form.submit(); }">
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
                            <option value="realizado" <?= $jogo['status'] == 'realizado' ? 'selected' : '' ?>>Realizado</option>
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
