<?php
/**
 * API - Forma Recente dos Clubes
 * Retorna os últimos 5 resultados de um clube (V/E/D)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// Validar clube_id
if (!isset($_GET['clube_id']) || !is_numeric($_GET['clube_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'clube_id inválido']);
    exit;
}

$clube_id = (int)$_GET['clube_id']; // Converter para inteiro

try {
    // Buscar forma recente diretamente da tabela classificacoes
    $stmt = $pdo->prepare("
        SELECT cl.forma
        FROM classificacoes cl
        WHERE cl.clube_id = :clube_id
        LIMIT 1
    ");
    
    $stmt->execute(['clube_id' => $clube_id]); // Executa a query preparada, passando o clube_id como parâmetro
    $resultado = $stmt->fetch();
    
    // Processar forma (string como "VVEDV")
    $forma_array = [];
    
    if ($resultado && !empty($resultado['forma'])) {
        // Converter string "VVEDV" ou "V V E D V" em array ['V', 'V', 'E', 'D', 'V']
        $forma_string = strtoupper(trim($resultado['forma']));
        // Remover espaços
        $forma_string = str_replace(' ', '', $forma_string);
        $forma_array = str_split($forma_string);
        
        // Limitar a 5 resultados
        $forma_array = array_slice($forma_array, 0, 5);
    }
    
    // Se não houver 5 jogos, preencher com null
    while (count($forma_array) < 5) {
        $forma_array[] = null;
    }
    
    echo json_encode([ // Retorna JSON com os dados
        'success' => true,
        'clube_id' => $clube_id,
        'forma' => $forma_array,
        'total_jogos' => count(array_filter($forma_array)) // array_filter remove nulls do array
    ], JSON_UNESCAPED_UNICODE); // JSON_UNESCAPED_UNICODE para manter caracteres UTF-8
    
} catch (PDOException $e) {
    error_log('Erro forma-recente.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar dados',
        'debug' => $e->getMessage() // Remover em produção
    ], JSON_UNESCAPED_UNICODE);
}
