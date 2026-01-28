<?php
require_once __DIR__ . '/../includes/config.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /vivedistrital/admin/admin-clubes.php');
    exit;
}

require_once __DIR__ . '/../config-clubes.php';

try {
    // Validar dados recebidos
    // INPUT_POST serve para obter o valor do campo enviado via POST
    // FILTER_VALIDATE_INT valida se é um inteiro
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $logo = trim($_POST['logo'] ?? '');
    $jogos = filter_input(INPUT_POST, 'jogos', FILTER_VALIDATE_INT);
    $pontos = filter_input(INPUT_POST, 'pontos', FILTER_VALIDATE_INT);
    $vitorias = filter_input(INPUT_POST, 'vitorias', FILTER_VALIDATE_INT);
    $empates = filter_input(INPUT_POST, 'empates', FILTER_VALIDATE_INT);
    $derrotas = filter_input(INPUT_POST, 'derrotas', FILTER_VALIDATE_INT);
    $golos_marcados = filter_input(INPUT_POST, 'golos_marcados', FILTER_VALIDATE_INT);
    $golos_sofridos = filter_input(INPUT_POST, 'golos_sofridos', FILTER_VALIDATE_INT);
    $forma = trim($_POST['forma'] ?? ''); // trim() remove espaços em branco no início e no fim da string

    // Validar campos obrigatórios
    if (!$id || $jogos === false || $pontos === false || $vitorias === false || 
        $empates === false || $derrotas === false || $golos_marcados === false || 
        $golos_sofridos === false || empty($logo)) {
        throw new Exception('Dados inválidos. Preencha todos os campos corretamente.');
    }

    // Verificar se o clube existe
    $clube = getClubeById($id);
    if (!$clube) {
        throw new Exception('Clube não encontrado.');
    }

    // Validações de lógica
    if ($jogos < 0 || $pontos < 0 || $vitorias < 0 || $empates < 0 || 
        $derrotas < 0 || $golos_marcados < 0 || $golos_sofridos < 0) {
        throw new Exception('Os valores não podem ser negativos.');
    }

    // Validar se jogos corresponde à soma de vitórias, empates e derrotas
    $total_resultados = $vitorias + $empates + $derrotas;
    if ($jogos !== $total_resultados) {
        throw new Exception("Número de jogos incorreto! A soma de vitórias ({$vitorias}), empates ({$empates}) e derrotas ({$derrotas}) é {$total_resultados}, mas os jogos indicados são {$jogos}. Devem ser iguais.");
    }

    // Validar pontos (Vitória = 3pts, Empate = 1pt, Derrota = 0pts)
    $pontos_calculados = ($vitorias * 3) + ($empates * 1);
    if ($pontos !== $pontos_calculados) {
        throw new Exception("Pontos incorretos! Com {$vitorias} vitórias e {$empates} empates, o clube deve ter {$pontos_calculados} pontos, não {$pontos}.");
    }

    // Preparar dados para atualização
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

    // Atualizar clube
    if (atualizarClube($id, $dados)) {
        $_SESSION['mensagem'] = "Clube '{$clube['nome']}' atualizado com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
    } else {
        throw new Exception('Erro ao atualizar o clube. Tente novamente.');
    }

} catch (Exception $e) {
    $_SESSION['mensagem'] = $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

// Redirecionar de volta
header('Location: /vivedistrital/admin/admin-clubes.php');
exit;
?>
