<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: text/html; charset=utf-8');

// Processar ação
$acao = $_POST['acao'] ?? '';
$mensagem = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar permissões
    if (!canManageNoticias()) {
        $_SESSION['mensagem'] = 'Não tens permissão para gerir notícias.';
        $_SESSION['tipo_mensagem'] = 'danger';
        header('Location: /vivedistrital/index.php');
        exit;
    }

    if ($acao === 'criar') {
        // Criar nova notícia
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        
        // Validações
        if (empty($titulo) || empty($resumo)) { // Verifica se os campos estão vazios
            $_SESSION['mensagem'] = 'Todos os campos são obrigatórios.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try { // Inserir no banco de dados das noticias
                $stmt = $pdo->prepare("
                    INSERT INTO noticias (titulo, resumo, conteudo, autor_id, data_publicacao)
                    VALUES (?, ?, '', ?, NOW())
                ");
                $stmt->execute([$titulo, $resumo, $_SESSION['user_id']]); // Usa o ID do usuário logado como autor
                
                $_SESSION['mensagem'] = 'Notícia criada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao criar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: /vivedistrital/admin/admin-noticias.php');
        exit;
    }
    
    if ($acao === 'editar') {
        // Editar notícia existente
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // INPUT_POST serve para obter o valor do campo 'id' enviado via POST, FILTER_VALIDATE_INT valida se é um inteiro
        $titulo = trim($_POST['titulo'] ?? ''); // trim() remove espaços em branco no início e no fim da string, edita o título
        $resumo = trim($_POST['resumo'] ?? ''); // edita o resumo
        
        // Validações
        if (!$id) {
            $_SESSION['mensagem'] = 'ID da notícia inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } elseif (empty($titulo) || empty($resumo)) {
            $_SESSION['mensagem'] = 'Todos os campos são obrigatórios.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try { // Atualizar no banco de dados das noticias, = ? são os valores que serão substituídos
                $stmt = $pdo->prepare("
                    UPDATE noticias 
                    SET titulo = ?, resumo = ?
                    WHERE id = ?
                ");
                $stmt->execute([$titulo, $resumo, $id]); // Executa a query com os valores fornecidos
                
                $_SESSION['mensagem'] = 'Notícia atualizada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao atualizar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: /vivedistrital/admin/admin-noticias.php');
        exit;
    }
    
    if ($acao === 'apagar') {
        // Apagar notícia
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['mensagem'] = 'ID da notícia inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try { // Apagar notícia do banco de dados
                $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
                $stmt->execute([$id]);
                
                $_SESSION['mensagem'] = 'Notícia apagada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao apagar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: /vivedistrital/admin/admin-noticias.php');
        exit;
    }
}

// Se chegou aqui sem POST, redirecionar
header('Location: /vivedistrital/admin/admin-noticias.php');
exit;
?>
