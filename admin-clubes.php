<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config-clubes.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
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
    <link rel="stylesheet" href="css/comum.css">
    <style>
        body {
            background: #0D0D0D;
            color: #fff;
        }
        .admin-container {
            max-width: 1400px;
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
        .clube-card {
            background: #1A1A1A;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }
        .clube-card:hover {
            border-color: #f1c40f;
            box-shadow: 0 0 20px rgba(241, 196, 15, 0.2);
        }
        .clube-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .clube-header img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
        }
        .clube-header h3 {
            color: #f1c40f;
            margin: 0;
            font-size: 1.5rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        .form-control {
            background: #0D0D0D;
            border: 1px solid #444;
            color: #fff;
            border-radius: 8px;
            padding: 10px;
        }
        .form-control:focus {
            background: #0D0D0D;
            border-color: #f1c40f;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
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
        .alert-success {
            background: #27ae60;
            border: none;
            border-radius: 10px;
            color: #fff;
        }
        .alert-danger {
            background: #e74c3c;
            border: none;
            border-radius: 10px;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>

        <div class="admin-header">
            <h1><i class="fas fa-users-cog"></i> Gestão de Clubes</h1>
            <p style="color: #999; margin: 10px 0 0 0;">Edite as estatísticas de cada clube abaixo</p>
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
                    <img src="<?= e($clube['logo']) ?>" alt="<?= e($clube['nome']) ?>" onerror="this.src='imgs/equipas/default.png'">
                    <div>
                        <h3><?= e($clube['nome']) ?></h3>
                        <small style="color: #999;">Código: <?= e($clube['codigo']) ?></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="logo_<?= $clube['id'] ?>">Logo (URL/Caminho)</label>
                        <input type="text" 
                               class="form-control" 
                               id="logo_<?= $clube['id'] ?>" 
                               name="logo" 
                               value="<?= e($clube['logo']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="jogos_<?= $clube['id'] ?>">Jogos</label>
                        <input type="number" 
                               class="form-control" 
                               id="jogos_<?= $clube['id'] ?>" 
                               name="jogos" 
                               value="<?= $clube['jogos'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="pontos_<?= $clube['id'] ?>">Pontos</label>
                        <input type="number" 
                               class="form-control" 
                               id="pontos_<?= $clube['id'] ?>" 
                               name="pontos" 
                               value="<?= $clube['pontos'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="vitorias_<?= $clube['id'] ?>">Vitórias</label>
                        <input type="number" 
                               class="form-control" 
                               id="vitorias_<?= $clube['id'] ?>" 
                               name="vitorias" 
                               value="<?= $clube['vitorias'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="empates_<?= $clube['id'] ?>">Empates</label>
                        <input type="number" 
                               class="form-control" 
                               id="empates_<?= $clube['id'] ?>" 
                               name="empates" 
                               value="<?= $clube['empates'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="derrotas_<?= $clube['id'] ?>">Derrotas</label>
                        <input type="number" 
                               class="form-control" 
                               id="derrotas_<?= $clube['id'] ?>" 
                               name="derrotas" 
                               value="<?= $clube['derrotas'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="gm_<?= $clube['id'] ?>">Golos Marcados</label>
                        <input type="number" 
                               class="form-control" 
                               id="gm_<?= $clube['id'] ?>" 
                               name="golos_marcados" 
                               value="<?= $clube['golos_marcados'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="gs_<?= $clube['id'] ?>">Golos Sofridos</label>
                        <input type="number" 
                               class="form-control" 
                               id="gs_<?= $clube['id'] ?>" 
                               name="golos_sofridos" 
                               value="<?= $clube['golos_sofridos'] ?? 0 ?>" 
                               min="0" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="forma_<?= $clube['id'] ?>">Forma (ex: V E D V V)</label>
                        <input type="text" 
                               class="form-control" 
                               id="forma_<?= $clube['id'] ?>" 
                               name="forma" 
                               value="<?= e($clube['forma'] ?? '') ?>" 
                               placeholder="V E D V V"
                               maxlength="50">
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
