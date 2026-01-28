<?php
/**
 * ADMIN - PROCESSAMENTO DE JOGOS (CRUD)
 * 
 * - CREATE: Adicionar novos jogos
 * - UPDATE: Editar jogos existentes (data, resultado, jornada)
 * - DELETE: Eliminar jogos
 * - DESTAQUE: Marcar jogo como destaque na página inicial
 * 
 * VALIDAÇÕES IMPLEMENTADAS:
 * 1. Prevenir clube jogar contra si próprio
 * 2. Prevenir jogos duplicados (mesma casa/fora)
 * 3. Permitir jogos de volta (casa ↔ fora invertidos)
 * 4. Validar resultados se status = finalizado
 * 5. Verificar permissões de administrador
 * 
 */

require_once __DIR__ . '/../includes/config.php';

// Verificar se utilizador tem privilégios de administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /vivedistrital/index.php');
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: /vivedistrital/admin/admin-jogos.php');
    exit;
}

require_once __DIR__ . '/../config-clubes.php';
$pdo = getDB();

try {
    // DEFINIR JOGO EM DESTAQUE
    if (isset($_GET['action']) && $_GET['action'] === 'set_destaque') { // $_GET é usado para obter o valor do parâmetro 'action' enviado via URL, && é o operador lógico "E"
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // INPUT_GET serve para obter o valor do campo 'id' enviado via GET, FILTER_VALIDATE_INT valida se é um inteiro
        
        if (!$id) {
            throw new Exception('ID inválido.');
        }
        
        // Primeiro, remover destaque de todos os jogos
        $stmt = $pdo->prepare("UPDATE jogos SET destaque = 0");
        $stmt->execute();
        
        // Depois, marcar este jogo como destaque
        $stmt = $pdo->prepare("UPDATE jogos SET destaque = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['mensagem'] = "Jogo definido como destaque com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
        header('Location: /vivedistrital/admin/admin-jogos.php');
        exit;
    }
    
    // DELETE
    if (isset($_GET['action']) && $_GET['action'] === 'delete') { // $_GET é usado para obter o valor do parâmetro 'action' enviado via URL, && é o operador lógico "E"
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            throw new Exception('ID inválido.');
        }
        
        // Procurar clubes envolvidos ANTES de deletar
        $stmt = $pdo->prepare("SELECT clube_casa_id, clube_fora_id FROM jogos WHERE id = :id"); // :id porque é um parâmetro nomeado (mais seguro contra SQL Injection)
        $stmt->execute([':id' => $id]); // Executa a query com o valor do ID fornecido
        $jogo = $stmt->fetch();
        
        if (!$jogo) {
            throw new Exception('Jogo não encontrado.');
        }
        
        // Deletar jogo
        $stmt = $pdo->prepare("DELETE FROM jogos WHERE id = :id"); // :id porque é um parâmetro nomeado (mais seguro contra SQL Injection)
        $stmt->execute([':id' => $id]); // Executa a query com o valor do ID fornecido
        
        // RECALCULAR ESTATÍSTICAS AUTOMATICAMENTE
        atualizarEstatisticasClube($jogo['clube_casa_id']); // funções definidas em config-clubes.php
        atualizarEstatisticasClube($jogo['clube_fora_id']);
        recalcularPosicoes();
        
        $_SESSION['mensagem'] = "Jogo eliminado e estatísticas recalculadas!";
        $_SESSION['tipo_mensagem'] = 'success';
        header('Location: /vivedistrital/admin/admin-jogos.php');
        exit;
    }
    
    $action = $_POST['action'] ?? ''; // faz a captura da ação (add/edit)
    
    // ADICIONAR
    if ($action === 'add') { // adicionar novo jogo
        $clube_casa_id = filter_input(INPUT_POST, 'clube_casa_id', FILTER_VALIDATE_INT); 
        $clube_fora_id = filter_input(INPUT_POST, 'clube_fora_id', FILTER_VALIDATE_INT);
        $data_jogo = trim($_POST['data_jogo'] ?? ''); // trim($_POST) é usado para remover espaços em branco
        $jornada = filter_input(INPUT_POST, 'jornada', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'agendado');
        
        // Converter resultados vazios em NULL
        $resultado_casa_raw = trim($_POST['resultado_casa'] ?? '');
        $resultado_fora_raw = trim($_POST['resultado_fora'] ?? '');
        
        $resultado_casa = ($resultado_casa_raw === '') ? null : filter_var($resultado_casa_raw, FILTER_VALIDATE_INT);
        $resultado_fora = ($resultado_fora_raw === '') ? null : filter_var($resultado_fora_raw, FILTER_VALIDATE_INT);
        
        // Validações
        if (!$clube_casa_id || !$clube_fora_id) { // || é "OU"
            throw new Exception('Selecione ambos os clubes.');
        }
        
        if ($clube_casa_id === $clube_fora_id) {
            throw new Exception('Um clube não pode jogar contra si próprio.');
        }
        
        if (empty($data_jogo)) {
            throw new Exception('Data do jogo é obrigatória.');
        }
        
        if (!$jornada || $jornada < 1) {
            throw new Exception('Jornada inválida.');
        }
        
        
        // Impede adicionar o mesmo jogo duas vezes
        // Exemplo BLOQUEADO: Nogueirense (casa) vs Tocha (fora) repetido
        // Exemplo PERMITIDO: Tocha (casa) vs Nogueirense (fora) - Jogo de volta
        // Consulta verifica se já existe combinação exata (casa+fora)
        $stmt_check = $pdo->prepare(
            "SELECT COUNT(*) FROM jogos 
             WHERE clube_casa_id = :casa 
             AND clube_fora_id = :fora"
        );
        $stmt_check->execute([
            ':casa' => $clube_casa_id,
            ':fora' => $clube_fora_id
        ]);
        
        if ($stmt_check->fetchColumn() > 0) { // > 0 quer dizer que o stmt_check existe, ou seja estão repetidos
            throw new Exception('Este jogo já existe! O jogo de volta deve ter os clubes invertidos (casa ↔ fora).');
        }
        
        if (!in_array($status, ['agendado', 'finalizado'])) { // !in_array verifica se o valor não está no array, neste caso 'agendado' ou 'finalizado'
            throw new Exception('Status inválido.');
        }
        
        // Validar resultados se status for finalizado
        if ($status === 'finalizado') {
            if ($resultado_casa === null || $resultado_fora === null) {
                throw new Exception('Resultados são obrigatórios para jogos finalizados.');
            }
            if ($resultado_casa < 0 || $resultado_fora < 0) {
                throw new Exception('Resultados não podem ser negativos.');
            }
        }
        
        // INSERIR JOGO NA BASE DE DADOS
        // Usa parâmetros nomeados (:nome) para segurança SQL Injection (previne ataques)
        $sql = "INSERT INTO jogos (clube_casa_id, clube_fora_id, data_jogo, jornada, resultado_casa, resultado_fora, status) 
                VALUES (:clube_casa_id, :clube_fora_id, :data_jogo, :jornada, :resultado_casa, :resultado_fora, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':clube_casa_id' => $clube_casa_id,
            ':clube_fora_id' => $clube_fora_id,
            ':data_jogo' => $data_jogo,
            ':jornada' => $jornada,
            ':resultado_casa' => $resultado_casa,
            ':resultado_fora' => $resultado_fora,
            ':status' => $status
        ]);
        
        // Sempre que um jogo é adicionado/editado/eliminado,
        // as estatísticas dos clubes envolvidos são recalculadas:
        // 1. atualizarEstatisticasClube() - Calcula e grava stats do clube
        // 2. recalcularPosicoes() - Reordena toda a classificação
        // Funções definidas em: config-clubes.php
        atualizarEstatisticasClube($clube_casa_id);
        atualizarEstatisticasClube($clube_fora_id);
        recalcularPosicoes();
        
        $_SESSION['mensagem'] = "Jogo adicionado e estatísticas recalculadas!";
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
        
        // PREVENIR DUPLICAÇÕES AO EDITAR
        // Buscar os clubes do jogo atual
        $stmt_jogo = $pdo->prepare("SELECT clube_casa_id, clube_fora_id FROM jogos WHERE id = :id");
        $stmt_jogo->execute([':id' => $id]);
        $jogo_atual = $stmt_jogo->fetch();
        
        if (!$jogo_atual) {
            throw new Exception('Jogo não encontrado.');
        }
        
        // Verificar se já existe outro jogo com mesma combinação (independente de jornada/data)
        $stmt_check = $pdo->prepare(
            "SELECT COUNT(*) FROM jogos 
             WHERE clube_casa_id = :casa 
             AND clube_fora_id = :fora 
             AND id != :id"
        );
        $stmt_check->execute([ // stmt_check é usado para executar a query preparada (verificar duplicação)
            ':casa' => $jogo_atual['clube_casa_id'],
            ':fora' => $jogo_atual['clube_fora_id'],
            ':id' => $id
        ]);
        
        if ($stmt_check->fetchColumn() > 0) { // > 0 quer dizer que o stmt_check existe, ou seja estão repetidos
            throw new Exception('Já existe outro jogo com esta combinação de clubes. Edite o jogo existente.');
        }
        
        if (!in_array($status, ['agendado', 'finalizado'])) {
            throw new Exception('Status inválido.');
        }
        
        // Validar resultados se status for finalizado
        if ($status === 'finalizado') {
            if ($resultado_casa === null || $resultado_fora === null) {
                throw new Exception('Resultados são obrigatórios para jogos finalizados.');
            }
            if ($resultado_casa < 0 || $resultado_fora < 0) {
                throw new Exception('Resultados não podem ser negativos.');
            }
        }
        
        // Procurar clubes envolvidos para recalcular
        $stmt = $pdo->prepare("SELECT clube_casa_id, clube_fora_id FROM jogos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $jogo_atual = $stmt->fetch();
        
        if (!$jogo_atual) {
            throw new Exception('Jogo não encontrado.');
        }
        
        // Atualizar
        $sql = "UPDATE jogos SET 
                    data_jogo = :data_jogo,
                    jornada = :jornada,
                    resultado_casa = :resultado_casa,
                    resultado_fora = :resultado_fora,
                    status = :status
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':data_jogo' => $data_jogo,
            ':jornada' => $jornada,
            ':resultado_casa' => $resultado_casa,
            ':resultado_fora' => $resultado_fora,
            ':status' => $status,
            ':id' => $id
        ]);
        
        // RECALCULAR ESTATÍSTICAS AUTOMATICAMENTE
        atualizarEstatisticasClube($jogo_atual['clube_casa_id']);
        atualizarEstatisticasClube($jogo_atual['clube_fora_id']);
        recalcularPosicoes();
        
        $_SESSION['mensagem'] = "Jogo atualizado e estatísticas recalculadas!";
        $_SESSION['tipo_mensagem'] = 'success';
    }
    
    else {
        throw new Exception('Ação inválida.');
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

header('Location: /vivedistrital/admin/admin-jogos.php');
exit;
?>
