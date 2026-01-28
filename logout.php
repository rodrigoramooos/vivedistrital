<?php
require_once __DIR__ . '/includes/config.php';

// Destruir a sessão
session_destroy();

// Redirecionar para a página inicial
header('Location: /vivedistrital/index.php');
exit;
?>
