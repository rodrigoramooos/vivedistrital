<?php
require_once 'includes/config.php';
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
        header('Location: ' . url('index.php'));
        exit;
    }

    if ($acao === 'criar') {
        // Criar nova notícia
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'fire');
        
        // Validações
        if (empty($titulo) || empty($resumo)) {
            $_SESSION['mensagem'] = 'Todos os campos são obrigatórios.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO noticias (titulo, resumo, conteudo, categoria, autor_id, data_publicacao)
                    VALUES (?, ?, '', ?, ?, NOW())
                ");
                $stmt->execute([$titulo, $resumo, $categoria, $_SESSION['user_id']]);
                
                $_SESSION['mensagem'] = 'Notícia criada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao criar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: ' . url('admin-noticias.php'));
        exit;
    }
    
    if ($acao === 'editar') {
        // Editar notícia existente
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'fire');
        
        // Validações
        if (!$id) {
            $_SESSION['mensagem'] = 'ID da notícia inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } elseif (empty($titulo) || empty($resumo)) {
            $_SESSION['mensagem'] = 'Todos os campos são obrigatórios.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE noticias 
                    SET titulo = ?, resumo = ?, categoria = ?
                    WHERE id = ?
                ");
                $stmt->execute([$titulo, $resumo, $categoria, $id]);
                
                $_SESSION['mensagem'] = 'Notícia atualizada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao atualizar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: ' . url('admin-noticias.php'));
        exit;
    }
    
    if ($acao === 'apagar') {
        // Apagar notícia
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['mensagem'] = 'ID da notícia inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
                $stmt->execute([$id]);
                
                $_SESSION['mensagem'] = 'Notícia apagada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = 'Erro ao apagar notícia: ' . $e->getMessage();
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
        header('Location: ' . url('admin-noticias.php'));
        exit;
    }
}

// Se chegou aqui sem POST, redirecionar
header('Location: ' . url('admin-noticias.php'));
exit;
?>
