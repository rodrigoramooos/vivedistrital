<?php
session_start();

define('SITE_NAME', 'Vive Distrital');
define('BASE_URL', '/vivedistrital/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'vivedistrital');
define('DB_USER', 'root');
define('DB_PASS', '');

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

function url($path = '') {
    return BASE_URL . $path;
}

function isActive($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return ($current == $page) ? 'active' : '';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getLoggedUser() {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT u.*, c.nome as clube_favorito_nome, c.codigo as clube_favorito_codigo, c.logo as clube_favorito_logo FROM utilizadores u LEFT JOIN clubes c ON u.clube_favorito_id = c.id WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getLoggedUser();
    return $user && $user['is_admin'] == 1;
}

function isJornalista() {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getLoggedUser();
    return $user && ($user['is_jornalista'] ?? 0) == 1;
}

function canManageNoticias() {
    return isAdmin() || isJornalista();
}

function getUnreadNotifications() {
    global $pdo;
    if (!isLoggedIn()) {
        return [];
    }
    
    $stmt = $pdo->prepare("SELECT * FROM notificacoes WHERE utilizador_id = ? AND lida = 0 ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

function countUnreadNotifications() {
    $notifications = getUnreadNotifications();
    return count($notifications);
}

function getProximosJogosClubeFavorito() {
    global $pdo;
    if (!isLoggedIn()) {
        return [];
    }
    
    $user = getLoggedUser();
    if (!$user || !$user['clube_favorito_id']) {
        return [];
    }
    
    $stmt = $pdo->prepare("SELECT j.*, cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo, cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo FROM jogos j INNER JOIN clubes cc ON j.clube_casa_id = cc.id INNER JOIN clubes cf ON j.clube_fora_id = cf.id WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?) AND j.status = 'agendado' AND j.data_jogo >= NOW() ORDER BY j.data_jogo ASC LIMIT 10");
    $stmt->execute([$user['clube_favorito_id'], $user['clube_favorito_id']]);
    return $stmt->fetchAll();
}

function getResultadosRecentesClubeFavorito() {
    global $pdo;
    if (!isLoggedIn()) {
        return [];
    }
    
    $user = getLoggedUser();
    if (!$user || !$user['clube_favorito_id']) {
        return [];
    }
    
    $stmt = $pdo->prepare("SELECT j.*, cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo, cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo FROM jogos j INNER JOIN clubes cc ON j.clube_casa_id = cc.id INNER JOIN clubes cf ON j.clube_fora_id = cf.id WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?) AND j.status = 'finalizado' ORDER BY j.data_jogo DESC LIMIT 10");
    $stmt->execute([$user['clube_favorito_id'], $user['clube_favorito_id']]);
    return $stmt->fetchAll();
}

function getNoticias($limit = null) {
    global $pdo;
    $sql = "SELECT n.*, u.username as autor_nome FROM noticias n INNER JOIN utilizadores u ON n.autor_id = u.id ORDER BY n.data_publicacao DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getNoticiaById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT n.*, u.username as autor_nome FROM noticias n INNER JOIN utilizadores u ON n.autor_id = u.id WHERE n.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
?>
