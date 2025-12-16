<?php
require_once 'includes/config.php';

// Destruir a sessão
session_destroy();

// Redirecionar para a página inicial
header('Location: ' . url('index.php'));
exit;
?>
