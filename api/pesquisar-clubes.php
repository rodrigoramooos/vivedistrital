<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($termo) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT nome, codigo, logo FROM clubes WHERE nome LIKE ? ORDER BY nome ASC LIMIT 10");
$searchTerm = '%' . $termo . '%';
$stmt->execute([$searchTerm]);
$clubes = $stmt->fetchAll();

echo json_encode($clubes);
