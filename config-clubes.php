<?php
/**
 * Configuração e Funções Auxiliares - Vive Distrital
 * Sistema Dinâmico de Gestão de Clubes
 */

// Importar configuração existente
require_once __DIR__ . '/includes/config.php';

/**
 * Obter ligação PDO da configuração principal
 * @return PDO
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * Obter todos os clubes ordenados por classificação
 * @return array
 */
function getClubes() {
    $pdo = getDB();
    $sql = "SELECT c.*, 
                   COALESCE(cl.pontos, 0) as pontos,
                   COALESCE(cl.jogos, 0) as jogos,
                   COALESCE(cl.vitorias, 0) as vitorias,
                   COALESCE(cl.empates, 0) as empates,
                   COALESCE(cl.derrotas, 0) as derrotas,
                   COALESCE(cl.golos_marcados, 0) as golos_marcados,
                   COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
                   COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
                   COALESCE(cl.posicao, 999) as posicao,
                   cl.forma as forma
            FROM clubes c
            LEFT JOIN classificacoes cl ON c.id = cl.clube_id
            ORDER BY COALESCE(cl.pontos, 0) DESC, 
                     COALESCE(cl.diferenca_golos, 0) DESC, 
                     COALESCE(cl.vitorias, 0) DESC, 
                     COALESCE(cl.golos_marcados, 0) DESC";
    return $pdo->query($sql)->fetchAll();
}

/**
 * Obter clube por código
 * @param string $codigo
 * @return array|false
 */
function getClubeByCodigo($codigo) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COALESCE(cl.pontos, 0) as pontos,
               COALESCE(cl.jogos, 0) as jogos,
               COALESCE(cl.vitorias, 0) as vitorias,
               COALESCE(cl.empates, 0) as empates,
               COALESCE(cl.derrotas, 0) as derrotas,
               COALESCE(cl.golos_marcados, 0) as golos_marcados,
               COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
               COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
               COALESCE(cl.posicao, 999) as posicao,
               cl.forma as forma
        FROM clubes c
        LEFT JOIN classificacoes cl ON c.id = cl.clube_id
        WHERE c.codigo = ? 
        LIMIT 1
    ");
    $stmt->execute([$codigo]);
    return $stmt->fetch();
}

/**
 * Obter clube por ID
 * @param int $id
 * @return array|false
 */
function getClubeById($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COALESCE(cl.pontos, 0) as pontos,
               COALESCE(cl.jogos, 0) as jogos,
               COALESCE(cl.vitorias, 0) as vitorias,
               COALESCE(cl.empates, 0) as empates,
               COALESCE(cl.derrotas, 0) as derrotas,
               COALESCE(cl.golos_marcados, 0) as golos_marcados,
               COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
               COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
               COALESCE(cl.posicao, 999) as posicao,
               cl.forma as forma
        FROM clubes c
        LEFT JOIN classificacoes cl ON c.id = cl.clube_id
        WHERE c.id = ? 
        LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Atualizar dados de um clube
 * @param int $id
 * @param array $dados
 * @return bool
 */
function atualizarClube($id, $dados) {
    $pdo = getDB();
    
    // Atualizar logo na tabela clubes se fornecido
    if (isset($dados['logo'])) {
        $stmt = $pdo->prepare("UPDATE clubes SET logo = ? WHERE id = ?");
        $stmt->execute([$dados['logo'], $id]);
    }
    
    // Atualizar estatísticas na tabela classificacoes
    $sql = "INSERT INTO classificacoes 
                (clube_id, posicao, jogos, pontos, vitorias, empates, derrotas, 
                 golos_marcados, golos_sofridos, diferenca_golos, forma)
            VALUES (?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                jogos = VALUES(jogos),
                pontos = VALUES(pontos),
                vitorias = VALUES(vitorias),
                empates = VALUES(empates),
                derrotas = VALUES(derrotas),
                golos_marcados = VALUES(golos_marcados),
                golos_sofridos = VALUES(golos_sofridos),
                diferenca_golos = VALUES(diferenca_golos),
                forma = VALUES(forma)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $id,
        $dados['jogos'] ?? 0,
        $dados['pontos'] ?? 0,
        $dados['vitorias'] ?? 0,
        $dados['empates'] ?? 0,
        $dados['derrotas'] ?? 0,
        $dados['golos_marcados'] ?? 0,
        $dados['golos_sofridos'] ?? 0,
        ($dados['golos_marcados'] ?? 0) - ($dados['golos_sofridos'] ?? 0),
        $dados['forma'] ?? null
    ]);
}

/**
 * Escapar HTML para prevenir XSS
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Calcular posição do clube
 * @param string $codigo
 * @return int
 */
function getPosicao($codigo) {
    $clubes = getClubes();
    foreach ($clubes as $index => $clube) {
        if ($clube['codigo'] === $codigo) {
            return $index + 1;
        }
    }
    return 0;
}

/**
 * Formatar forma em HTML
 * @param string $forma
 * @return string
 */
function formatarForma($forma) {
    if (empty($forma)) return '';
    
    $html = '<div class="forma">';
    $partes = explode(' ', trim($forma));
    
    foreach ($partes as $resultado) {
        $resultado = strtoupper(trim($resultado));
        $classe = match($resultado) {
            'V' => 'vitoria',
            'E' => 'empate',
            'D' => 'derrota',
            default => 'nao-jogado'
        };
        $html .= "<span class='$classe'>$resultado</span>";
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Formatar diferença de golos
 * @param int $diferenca
 * @return string
 */
function formatarDG($diferenca) {
    return ($diferenca > 0 ? '+' : '') . $diferenca;
}
?>
