<?php
require_once 'includes/config.php';

// Verificar se utilizador é admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: admin-jogos.php');
    exit;
}

require_once 'config-clubes.php';
$pdo = getDB();

try {
    // DELETE
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            throw new Exception('ID inválido.');
        }
        
        $stmt = $pdo->prepare("DELETE FROM jogos WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['mensagem'] = "Jogo eliminado com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
        header('Location: admin-jogos.php');
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    
    // ADICIONAR
    if ($action === 'add') {
        $clube_casa_id = filter_input(INPUT_POST, 'clube_casa_id', FILTER_VALIDATE_INT);
        $clube_fora_id = filter_input(INPUT_POST, 'clube_fora_id', FILTER_VALIDATE_INT);
        $data_jogo = trim($_POST['data_jogo'] ?? '');
        $jornada = filter_input(INPUT_POST, 'jornada', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'agendado');
        
        // Converter resultados vazios em NULL
        $resultado_casa_raw = trim($_POST['resultado_casa'] ?? '');
        $resultado_fora_raw = trim($_POST['resultado_fora'] ?? '');
        
        $resultado_casa = ($resultado_casa_raw === '') ? null : filter_var($resultado_casa_raw, FILTER_VALIDATE_INT);
        $resultado_fora = ($resultado_fora_raw === '') ? null : filter_var($resultado_fora_raw, FILTER_VALIDATE_INT);
        
        // Validações
        if (!$clube_casa_id || !$clube_fora_id) {
            throw new Exception('Selecione ambos os clubes.');
        }
        
        if ($clube_casa_id === $clube_fora_id) {
            throw new Exception('Os clubes não podem ser iguais.');
        }
        
        if (empty($data_jogo)) {
            throw new Exception('Data do jogo é obrigatória.');
        }
        
        if (!$jornada || $jornada < 1) {
            throw new Exception('Jornada inválida.');
        }
        
        if (!in_array($status, ['agendado', 'realizado', 'cancelado'])) {
            throw new Exception('Status inválido.');
        }
        
        // Validar resultados se status for realizado
        if ($status === 'realizado') {
            if ($resultado_casa === null || $resultado_fora === null) {
                throw new Exception('Resultados são obrigatórios para jogos realizados.');
            }
            if ($resultado_casa < 0 || $resultado_fora < 0) {
                throw new Exception('Resultados não podem ser negativos.');
            }
        }
        
        // Inserir
        $sql = "INSERT INTO jogos (clube_casa_id, clube_fora_id, data_jogo, jornada, resultado_casa, resultado_fora, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $clube_casa_id,
            $clube_fora_id,
            $data_jogo,
            $jornada,
            $resultado_casa,
            $resultado_fora,
            $status
        ]);
        
        $_SESSION['mensagem'] = "Jogo adicionado com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
    }
    
    // EDITAR
    else if ($action === 'edit') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data_jogo = trim($_POST['data_jogo'] ?? '');
        $jornada = filter_input(INPUT_POST, 'jornada', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'agendado');
        
        // Converter resultados vazios em NULL
        $resultado_casa_raw = trim($_POST['resultado_casa'] ?? '');
        $resultado_fora_raw = trim($_POST['resultado_fora'] ?? '');
        
        $resultado_casa = ($resultado_casa_raw === '') ? null : filter_var($resultado_casa_raw, FILTER_VALIDATE_INT);
        $resultado_fora = ($resultado_fora_raw === '') ? null : filter_var($resultado_fora_raw, FILTER_VALIDATE_INT);
        
        // Validações
        if (!$id) {
            throw new Exception('ID inválido.');
        }
        
        if (empty($data_jogo)) {
            throw new Exception('Data do jogo é obrigatória.');
        }
        
        if (!$jornada || $jornada < 1) {
            throw new Exception('Jornada inválida.');
        }
        
        if (!in_array($status, ['agendado', 'realizado', 'cancelado'])) {
            throw new Exception('Status inválido.');
        }
        
        // Validar resultados se status for realizado
        if ($status === 'realizado') {
            if ($resultado_casa === null || $resultado_fora === null) {
                throw new Exception('Resultados são obrigatórios para jogos realizados.');
            }
            if ($resultado_casa < 0 || $resultado_fora < 0) {
                throw new Exception('Resultados não podem ser negativos.');
            }
        }
        
        // Verificar se o jogo existe
        $stmt = $pdo->prepare("SELECT id FROM jogos WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('Jogo não encontrado.');
        }
        
        // Atualizar
        $sql = "UPDATE jogos SET 
                    data_jogo = ?,
                    jornada = ?,
                    resultado_casa = ?,
                    resultado_fora = ?,
                    status = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data_jogo,
            $jornada,
            $resultado_casa,
            $resultado_fora,
            $status,
            $id
        ]);
        
        $_SESSION['mensagem'] = "Jogo atualizado com sucesso!";
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
