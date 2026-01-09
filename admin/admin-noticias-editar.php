<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: text/html; charset=utf-8');

$acao = $_POST['acao'] ?? '';
$mensagem = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!canManageNoticias()) {
        $_SESSION['mensagem'] = 'Não tens permissão para gerir notícias.';
        $_SESSION['tipo_mensagem'] = 'danger';
        header('Location: ' . url('index.php'));
        exit;
    }

    if ($acao === 'criar') {
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'fire');
        
        if (empty($titulo) || empty($resumo)) {
            $_SESSION['mensagem'] = 'Preencha todos os campos.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, resumo, conteudo, categoria, autor_id, data_publicacao) VALUES (?, ?, '', ?, ?, NOW())");
            $stmt->execute([$titulo, $resumo, $categoria, $_SESSION['user_id']]);
            $_SESSION['mensagem'] = 'Notícia criada!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
        header('Location: ' . url('admin/admin-noticias.php'));
        exit;
    }
    
    if ($acao === 'editar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'fire');
        
        if (!$id || empty($titulo) || empty($resumo)) {
            $_SESSION['mensagem'] = 'Dados inválidos.';
            $_SESSION['tipo_mensagem'] = 'danger';
        } else {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, resumo = ?, categoria = ? WHERE id = ?");
            $stmt->execute([$titulo, $resumo, $categoria, $id]);
            $_SESSION['mensagem'] = 'Notícia atualizada!';
            $_SESSION['tipo_mensagem'] = 'success';
        }
        header('Location: ' . url('admin/admin-noticias.php'));
        exit;
    }
    
    if ($acao === 'apagar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensagem'] = 'Notícia apagada!';
            $_SESSION['tipo_mensagem'] = 'success';
        } else {
            $_SESSION['mensagem'] = 'ID inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
        }
        header('Location: ' . url('admin/admin-noticias.php'));
        exit;
    }
}

header('Location: ' . url('admin/admin-noticias.php'));
exit;
?>