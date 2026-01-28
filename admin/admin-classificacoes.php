<?php
require_once __DIR__ . '/../includes/config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /vivedistrital/login.php');
    exit;
}

$message = ''; // Mensagem de feedback
$messageType = '';

// Processar atualização de classificação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_classificacao') {
    $clube_id = $_POST['clube_id'];
    $posicao = $_POST['posicao'];
    $jogos = $_POST['jogos'];
    $vitorias = $_POST['vitorias'];
    $empates = $_POST['empates'];
    $derrotas = $_POST['derrotas'];
    $golos_marcados = $_POST['golos_marcados'];
    $golos_sofridos = $_POST['golos_sofridos'];
    
    // Calcular diferença de golos e pontos
    $diferenca_golos = $golos_marcados - $golos_sofridos;
    $pontos = ($vitorias * 3) + $empates; 
    
    try {
        // Verificar se já existe registo
        $stmt = $pdo->prepare("SELECT id FROM classificacoes WHERE clube_id = ?");
        $stmt->execute([$clube_id]); // Executa a query preparada, passando o clube_id como parâmetro
        $exists = $stmt->fetch(); // Verifica se existe registo para este clube
        
        if ($exists) {
            // Atualizar
            $stmt = $pdo->prepare("
                UPDATE classificacoes 
                SET posicao = ?, jogos = ?, vitorias = ?, empates = ?, derrotas = ?, 
                    golos_marcados = ?, golos_sofridos = ?, diferenca_golos = ?, pontos = ?
                WHERE clube_id = ?
            ");
            $stmt->execute([$posicao, $jogos, $vitorias, $empates, $derrotas, $golos_marcados, $golos_sofridos, $diferenca_golos, $pontos, $clube_id]);
        } else {
            // Inserir
            $stmt = $pdo->prepare("
                INSERT INTO classificacoes (clube_id, posicao, jogos, vitorias, empates, derrotas, golos_marcados, golos_sofridos, diferenca_golos, pontos)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$clube_id, $posicao, $jogos, $vitorias, $empates, $derrotas, $golos_marcados, $golos_sofridos, $diferenca_golos, $pontos]);
        }
        
        $message = 'Classificação atualizada com sucesso!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Erro ao atualizar classificação: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obter todos os clubes com suas classificações
// IFNULL é usado para garantir que, se não houver classificação, os valores padrão sejam 0
// LEFT JOIN é usado para incluir todos os clubes, mesmo que não tenham classificação
// cl.posicao ASC para ordenar do 1º ao último, DESC para pontos, e c.nome a aparecer em ordem alfabética se houver empate
try {
    $stmt = $pdo->query("
        SELECT c.id, c.nome, c.codigo, c.logo,
               IFNULL(cl.posicao, 0) as posicao,
               IFNULL(cl.jogos, 0) as jogos,
               IFNULL(cl.vitorias, 0) as vitorias,
               IFNULL(cl.empates, 0) as empates,
               IFNULL(cl.derrotas, 0) as derrotas,
               IFNULL(cl.golos_marcados, 0) as golos_marcados,
               IFNULL(cl.golos_sofridos, 0) as golos_sofridos,
               IFNULL(cl.diferenca_golos, 0) as diferenca_golos,
               IFNULL(cl.pontos, 0) as pontos
        FROM clubes c
        LEFT JOIN classificacoes cl ON c.id = cl.clube_id
        ORDER BY cl.posicao ASC, cl.pontos DESC, c.nome ASC
    ");
    $clubes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clubes = [];
    $message = 'Erro ao carregar clubes: ' . $e->getMessage();
    $messageType = 'danger';
}

$pageTitle = 'Gestão de Classificações';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="/vivedistrital/css/comum.css">
    <link rel="stylesheet" href="/vivedistrital/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-trophy"></i> Gestão de Classificações</h1>
                    <p>Atualize as classificações dos clubes após cada jornada</p>
                </div>
                <a href="/vivedistrital/admin/admin.php" class="btn btn-dark">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="classificacao-table">
            <h4><i class="fas fa-list-ol"></i> Classificações - Editar</h4>
            <p style="color: #888; font-size: 0.9rem; margin-bottom: 1.5rem;">
                <i class="fas fa-info-circle"></i> Os pontos são calculados automaticamente (Vitória = 3 pts, Empate = 1 pt)
            </p>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Pos.</th>
                            <th>Clube</th>
                            <th style="width: 80px;">J</th>
                            <th style="width: 80px;">V</th>
                            <th style="width: 80px;">E</th>
                            <th style="width: 80px;">D</th>
                            <th style="width: 80px;">GM</th>
                            <th style="width: 80px;">GS</th>
                            <th style="width: 80px;">DG</th>
                            <th style="width: 100px;">Pts</th>
                            <th style="width: 120px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clubes as $clube): ?>
                        <form method="POST" style="display: contents;">
                            <input type="hidden" name="action" value="update_classificacao">
                            <input type="hidden" name="clube_id" value="<?php echo $clube['id']; ?>">
                            
                            <tr>
                                <td>
                                    <input type="number" name="posicao" value="<?php echo $clube['posicao']; ?>" 
                                           class="form-control form-control-sm" min="1" max="20" required>
                                </td>
                                <td>
                                    <div class="clube-info">
                                        <img src="/vivedistrital/<?php echo $clube['logo']; ?>" alt="<?php echo $clube['nome']; ?>">
                                        <strong><?php echo htmlspecialchars($clube['nome']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" name="jogos" value="<?php echo $clube['jogos'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="vitorias" value="<?php echo $clube['vitorias'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="empates" value="<?php echo $clube['empates'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="derrotas" value="<?php echo $clube['derrotas'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="golos_marcados" value="<?php echo $clube['golos_marcados'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="golos_sofridos" value="<?php echo $clube['golos_sofridos'] ?? 0; ?>" 
                                           class="form-control form-control-sm" min="0" required>
                                </td>
                                <td>
                                    <span style="color: <?php echo ($clube['diferenca_golos'] ?? 0) >= 0 ? '#27ae60' : '#e74c3c'; ?>">
                                        <?php echo ($clube['diferenca_golos'] ?? 0) >= 0 ? '+' : ''; ?><?php echo $clube['diferenca_golos'] ?? 0; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #f1c40f; font-size: 1.1rem;">
                                        <?php echo $clube['pontos'] ?? 0; ?>
                                    </strong>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-save btn-sm">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                </td>
                            </tr>
                        </form>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
