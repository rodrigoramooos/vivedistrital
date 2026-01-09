<?php
require_once __DIR__ . '/../includes/config.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-clubes.php');
    exit;
}

require_once __DIR__ . '/../config-clubes.php';

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $logo = trim($_POST['logo'] ?? '');
    $jogos = filter_input(INPUT_POST, 'jogos', FILTER_VALIDATE_INT);
    $pontos = filter_input(INPUT_POST, 'pontos', FILTER_VALIDATE_INT);
    $vitorias = filter_input(INPUT_POST, 'vitorias', FILTER_VALIDATE_INT);
    $empates = filter_input(INPUT_POST, 'empates', FILTER_VALIDATE_INT);
    $derrotas = filter_input(INPUT_POST, 'derrotas', FILTER_VALIDATE_INT);
    $golos_marcados = filter_input(INPUT_POST, 'golos_marcados', FILTER_VALIDATE_INT);
    $golos_sofridos = filter_input(INPUT_POST, 'golos_sofridos', FILTER_VALIDATE_INT);
    $forma = trim($_POST['forma'] ?? '');

    if (!$id || $jogos === false || $pontos === false || $vitorias === false || 
        $empates === false || $derrotas === false || $golos_marcados === false || 
        $golos_sofridos === false || empty($logo)) {
        throw new Exception('Preencha todos os campos.');
    }

    $clube = getClubeById($id);
    if (!$clube) {
        throw new Exception('Clube não encontrado.');
    }

    if ($jogos < 0 || $pontos < 0 || $vitorias < 0 || $empates < 0 || 
        $derrotas < 0 || $golos_marcados < 0 || $golos_sofridos < 0) {
        throw new Exception('Valores não podem ser negativos.');
    }

    $total_resultados = $vitorias + $empates + $derrotas;
    if ($jogos !== $total_resultados) {
        throw new Exception("Jogos incorreto! V+E+D = {$total_resultados}, mas indicaste {$jogos}.");
    }

    $pontos_calculados = ($vitorias * 3) + $empates;
    if ($pontos !== $pontos_calculados) {
        throw new Exception("Pontos incorretos! Devem ser {$pontos_calculados}.");
    }

    $dados = [
        'logo' => $logo,
        'jogos' => $jogos,
        'pontos' => $pontos,
        'vitorias' => $vitorias,
        'empates' => $empates,
        'derrotas' => $derrotas,
        'golos_marcados' => $golos_marcados,
        'golos_sofridos' => $golos_sofridos,
        'forma' => $forma
    ];

    if (atualizarClube($id, $dados)) {
        $_SESSION['mensagem'] = "Clube atualizado!";
        $_SESSION['tipo_mensagem'] = 'success';
    } else {
        throw new Exception('Erro ao atualizar.');
    }

} catch (Exception $e) {
    $_SESSION['mensagem'] = $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

header('Location: admin-clubes.php');
exit;
?>
