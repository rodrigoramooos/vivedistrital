<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: admin-jogos.php');
    exit;
}

require_once __DIR__ . '/../config-clubes.php';
$pdo = getDB();

try {
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM jogos WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensagem'] = "Jogo eliminado!";
            $_SESSION['tipo_mensagem'] = 'success';
        }
        
        header('Location: admin-jogos.php');
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $clube_casa_id = filter_input(INPUT_POST, 'clube_casa_id', FILTER_VALIDATE_INT);
        $clube_fora_id = filter_input(INPUT_POST, 'clube_fora_id', FILTER_VALIDATE_INT);
        $data_jogo = trim($_POST['data_jogo'] ?? '');
        $jornada = filter_input(INPUT_POST, 'jornada', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'agendado');
        
        $resultado_casa_raw = trim($_POST['resultado_casa'] ?? '');
        $resultado_fora_raw = trim($_POST['resultado_fora'] ?? '');
        $resultado_casa = ($resultado_casa_raw === '') ? null : filter_var($resultado_casa_raw, FILTER_VALIDATE_INT);
        $resultado_fora = ($resultado_fora_raw === '') ? null : filter_var($resultado_fora_raw, FILTER_VALIDATE_INT);
        
        if (!$clube_casa_id || !$clube_fora_id || $clube_casa_id === $clube_fora_id || empty($data_jogo) || !$jornada) {
            throw new Exception('Dados inválidos.');
        }
        
        if ($status === 'finalizado' && ($resultado_casa === null || $resultado_fora === null || $resultado_casa < 0 || $resultado_fora < 0)) {
            throw new Exception('Resultados inválidos.');
        }
        
        $sql = "INSERT INTO jogos (clube_casa_id, clube_fora_id, data_jogo, jornada, resultado_casa, resultado_fora, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clube_casa_id, $clube_fora_id, $data_jogo, $jornada, $resultado_casa, $resultado_fora, $status]);
        
        $_SESSION['mensagem'] = "Jogo adicionado!";
        $_SESSION['tipo_mensagem'] = 'success';
    }
    
    else if ($action === 'edit') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data_jogo = trim($_POST['data_jogo'] ?? '');
        $jornada = filter_input(INPUT_POST, 'jornada', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'agendado');
        
        $resultado_casa_raw = trim($_POST['resultado_casa'] ?? '');
        $resultado_fora_raw = trim($_POST['resultado_fora'] ?? '');
        $resultado_casa = ($resultado_casa_raw === '') ? null : filter_var($resultado_casa_raw, FILTER_VALIDATE_INT);
        $resultado_fora = ($resultado_fora_raw === '') ? null : filter_var($resultado_fora_raw, FILTER_VALIDATE_INT);
        
        if (!$id || empty($data_jogo) || !$jornada) {
            throw new Exception('Dados inválidos.');
        }
        
        if ($status === 'finalizado' && ($resultado_casa === null || $resultado_fora === null || $resultado_casa < 0 || $resultado_fora < 0)) {
            throw new Exception('Resultados inválidos para jogo finalizado.');
        }
        
        $sql = "UPDATE jogos SET data_jogo = ?, jornada = ?, resultado_casa = ?, resultado_fora = ?, status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data_jogo, $jornada, $resultado_casa, $resultado_fora, $status, $id]);
        
        $_SESSION['mensagem'] = "Jogo atualizado!";
        $_SESSION['tipo_mensagem'] = 'success';
    }
    
    else {
        throw new Exception('Ação inválida.');
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

header('Location: admin-jogos.php');
exit;
?>
