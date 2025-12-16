<?php
require_once '../includes/config.php';

// Configurar cabeçalhos para JSON
header('Content-Type: application/json; charset=utf-8');

// Obter termo de pesquisa
$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

// Validar entrada
if (strlen($termo) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Pesquisar clubes que correspondem ao termo
    $stmt = $pdo->prepare("
        SELECT nome, codigo, logo 
        FROM clubes 
        WHERE nome LIKE ? 
        ORDER BY nome ASC 
        LIMIT 10
    ");
    
    $searchTerm = '%' . $termo . '%';
    $stmt->execute([$searchTerm]);
    $clubes = $stmt->fetchAll();
    
    // Retornar resultados
    echo json_encode($clubes);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao pesquisar clubes']);
}
