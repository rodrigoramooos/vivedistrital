<?php
/**
 * Script utilitário para recalcular todas as estatísticas
 * a partir dos jogos registados.
 * 
 */

require_once __DIR__ . '/../config-clubes.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /vivedistrital/index.php');
    exit;
}

$pdo = getDB();

try {
    // Buscar todos os clubes
    $clubes = $pdo->query("SELECT id, nome FROM clubes ORDER BY nome")->fetchAll();
    
    $resultados = []; // Armazenar resultados de cada clube
    
    foreach ($clubes as $clube) {
        // Recalcular estatísticas
        $stats = calcularEstatisticasClube($clube['id']);
        atualizarEstatisticasClube($clube['id']);
        
        $resultados[] = [ // Armazenar resultados, nome do clube e stats
            'nome' => $clube['nome'],
            'stats' => $stats
        ];
    }
    
    // Recalcular posições
    recalcularPosicoes();
    
    $_SESSION['mensagem'] = "Todas as estatísticas foram recalculadas com sucesso!";
    $_SESSION['tipo_mensagem'] = 'success';
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao recalcular: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

header('Location: /vivedistrital/admin/admin-clubes.php');
exit;
?>
