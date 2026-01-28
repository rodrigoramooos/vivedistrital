<?php
require_once __DIR__ . '/../config-clubes.php';

// Verificar se utilizador é admin ou se não está logado
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Processar mensagens
$mensagem = '';
$tipo_mensagem = '';

if (isset($_SESSION['mensagem'])) { // Processar mensagem de sessão
    $mensagem = $_SESSION['mensagem']; // Obter mensagem da sessão
    $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'success'; // Obter tipo de mensagem, padrão 'success'
    unset($_SESSION['mensagem']); // unset é usado para remover variáveis da sessão
    unset($_SESSION['tipo_mensagem']);
}

// Obter todos os jogos ordenados por data
// JOIN para obter nomes e logos dos clubes
// ORDER BY destaque DESC para mostrar jogos em destaque primeiro, depois por data
$pdo = getDB();
$stmt = $pdo->query("
    SELECT j.*, 
           cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
           cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo,
           j.destaque
    FROM jogos j
    JOIN clubes cc ON j.clube_casa_id = cc.id
    JOIN clubes cf ON j.clube_fora_id = cf.id
    ORDER BY j.destaque DESC, j.data_jogo DESC
");
$jogos = $stmt->fetchAll(); // Fetch (buscar) all jogos

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
                    <h1><i class="fas fa-calendar-alt"></i> Gestão de Jogos</h1>
                    <p style="color: #999; margin: 10px 0 0 0;">Adicione e edite os jogos da competição</p>
                </div> <!-- getElementById serve para obter um elemento pelo seu ID, scrollIntoView faz scroll suave -->
                <a href="#add-new-jogo" class="btn-add-jogo" onclick="document.getElementById('novo-jogo-form').scrollIntoView({behavior: 'smooth'});">
                    <i class="fas fa-plus"></i> Adicionar Novo Jogo
                </a>
            </div>
        </div>

        <?php if ($mensagem): // Mostrar mensagem de alerta ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                <?= e($mensagem) // Escapar a mensagem para evitar XSS ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulário para Adicionar Novo Jogo -->
        <div id="novo-jogo-form" class="jogo-card" style="border-color: #27ae60;">
            <form method="POST" action="/vivedistrital/admin/admin-jogos-editar.php">
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
                            <option value="finalizado">Finalizado</option>
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
            <form method="POST" action="/vivedistrital/admin/admin-jogos-editar.php" class="jogo-card">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $jogo['id'] ?>"> <!-- ID do jogo para edição -->
                
                <div class="jogo-header">
                    <div class="jogo-info"> <!-- foi usado e() para escapar dados e evitar XSS -->
                        <img src="<?= e($jogo['clube_casa_logo']) ?>" alt="<?= e($jogo['clube_casa_nome']) ?>" onerror="this.src='../imgs/equipas/<?= strtolower($jogo['clube_casa_codigo']) ?>.png'">
                        <strong><?= e($jogo['clube_casa_nome']) ?></strong>
                        <span class="versus">VS</span>
                        <strong><?= e($jogo['clube_fora_nome']) ?></strong>
                        <img src="<?= e($jogo['clube_fora_logo']) ?>" alt="<?= e($jogo['clube_fora_nome']) ?>" onerror="this.src='../imgs/equipas/<?= strtolower($jogo['clube_fora_codigo']) ?>.png'">
                    </div>
                    <div>
                        <span class="status-badge status-<?= $jogo['status'] ?>">
                            <?= ucfirst($jogo['status']) ?>
                        </span>
                        <?php if ($jogo['destaque'] == 1): // $jogo['destaque'] == 1 significa que está em destaque ?>
                            <span class="status-badge" style="background: #f1c40f; color: #000; font-weight: 700; margin-left: 0.5rem;">
                                <i class="fas fa-star"></i> EM DESTAQUE
                            </span>
                        <?php else: ?>
                            <button type="button" class="btn-destaque" onclick="if(confirm('Definir este jogo como destaque na página inicial?')) { window.location.href='/vivedistrital/admin/admin-jogos-editar.php?action=set_destaque&id=<?= $jogo['id'] ?>'; }" style="background: #f39c12; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; margin-left: 0.5rem; font-size: 0.85rem;">
                                <i class="fas fa-star"></i> Definir Destaque
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn-delete" onclick="if(confirm('Tem certeza que deseja eliminar este jogo?')) { this.form.action='/vivedistrital/admin/admin-jogos-editar.php?action=delete&id=<?= $jogo['id'] ?>'; this.form.submit(); }">
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
    
    <script>
    // VALIDAÇÃO: Prevenir clube jogar contra si próprio
    document.addEventListener('DOMContentLoaded', function() { // addEventListener é usado para anexar um evento a um elemento
        // Selecionar formulário de adicionar
        const formAdd = document.querySelector('#novo-jogo-form form');
        
        if (formAdd) { // formAdd para verificar se o formulário existe
            const selectCasa = formAdd.querySelector('select[name="clube_casa_id"]');
            const selectFora = formAdd.querySelector('select[name="clube_fora_id"]');
            
            function validarClubes() {
                if (selectCasa.value && selectFora.value && selectCasa.value === selectFora.value) {
                    selectFora.setCustomValidity('Um clube não pode jogar contra si próprio!');
                    selectFora.reportValidity();
                    return false;
                } else {
                    selectCasa.setCustomValidity('');
                    selectFora.setCustomValidity('');
                    return true;
                }
            }
            
            selectCasa.addEventListener('change', validarClubes); // Sempre que mudar, este vai validar os clubes
            selectFora.addEventListener('change', validarClubes);
            
            formAdd.addEventListener('submit', function(e) {
                if (!validarClubes()) {
                    e.preventDefault();
                }
            });
        }
    });
    </script>
</body>
</html>
