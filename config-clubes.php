<?php
require_once __DIR__ . '/includes/config.php';

function getDB() {
    global $pdo;
    return $pdo;
}

function getClubes() {
    $pdo = getDB();
    $sql = "SELECT c.*, cl.pontos, cl.jogos, cl.vitorias, cl.empates, cl.derrotas, cl.golos_marcados, cl.golos_sofridos, cl.diferenca_golos, cl.posicao, cl.forma FROM clubes c LEFT JOIN classificacoes cl ON c.id = cl.clube_id ORDER BY cl.pontos DESC, cl.diferenca_golos DESC, cl.vitorias DESC, cl.golos_marcados DESC";
    return $pdo->query($sql)->fetchAll();
}

function getClubeByCodigo($codigo) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.*, cl.pontos, cl.jogos, cl.vitorias, cl.empates, cl.derrotas, cl.golos_marcados, cl.golos_sofridos, cl.diferenca_golos, cl.posicao, cl.forma FROM clubes c LEFT JOIN classificacoes cl ON c.id = cl.clube_id WHERE c.codigo = ? LIMIT 1");
    $stmt->execute([$codigo]);
    return $stmt->fetch();
}

function getClubeById($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.*, cl.pontos, cl.jogos, cl.vitorias, cl.empates, cl.derrotas, cl.golos_marcados, cl.golos_sofridos, cl.diferenca_golos, cl.posicao, cl.forma FROM clubes c LEFT JOIN classificacoes cl ON c.id = cl.clube_id WHERE c.id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function atualizarClube($id, $dados) {
    $pdo = getDB();
    
    if (isset($dados['logo'])) {
        $stmt = $pdo->prepare("UPDATE clubes SET logo = ? WHERE id = ?");
        $stmt->execute([$dados['logo'], $id]);
    }
    
    $sql = "INSERT INTO classificacoes (clube_id, posicao, jogos, pontos, vitorias, empates, derrotas, golos_marcados, golos_sofridos, diferenca_golos, forma) VALUES (?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE jogos = VALUES(jogos), pontos = VALUES(pontos), vitorias = VALUES(vitorias), empates = VALUES(empates), derrotas = VALUES(derrotas), golos_marcados = VALUES(golos_marcados), golos_sofridos = VALUES(golos_sofridos), diferenca_golos = VALUES(diferenca_golos), forma = VALUES(forma)";
    
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

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function getPosicao($codigo) {
    $clubes = getClubes();
    foreach ($clubes as $index => $clube) {
        if ($clube['codigo'] === $codigo) {
            return $index + 1;
        }
    }
    return 0;
}

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

function formatarDG($diferenca) {
    return ($diferenca > 0 ? '+' : '') . $diferenca;
}
?>
