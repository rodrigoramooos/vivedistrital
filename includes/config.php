<?php
/**
 * CONFIGURAÇÃO GLOBAL DO SISTEMA - VIVE DISTRITAL
 */

// Permite armazenar dados entre páginas (login, mensagens flash, ...)
session_start();

// CONSTANTES DO SISTEMA
// Definir configurações globais acessíveis em qualquer página
define('SITE_NAME', 'Vive Distrital');      // Nome do portal
define('BASE_URL', '/vivedistrital/');       // URL base do projeto

// CREDENCIAIS DA BASE DE DADOS
define('DB_HOST', 'localhost');       // Servidor MySQL (Laragon local)
define('DB_NAME', 'vivedistrital');   // Nome da base de dados
define('DB_USER', 'root');            // Utilizador MySQL
define('DB_PASS', '');                // Password (vazia em desenvolvimento)

// CONEXÃO PDO COM MYSQL
// PDO (PHP Data Objects) - Interface segura para acesso a BD
try {
    // Criar instância PDO com configurações de segurança
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            // Lançar exceções em caso de erro (não avisos silenciosos)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            
            // Retornar arrays associativos por defeito ($row['coluna'])
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            
            // Desativar emulação de prepared statements (usar nativos do MySQL)
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Forçar charset UTF-8 em todas as queries
    // Garante suporte a: á, ã, ç, ó, ê, etc.
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    
} catch (PDOException $e) {
    // Em caso de erro, terminar execução e mostrar mensagem
    die("Erro na conexão com a base de dados: " . $e->getMessage());
}

// FUNÇÕES DO SISTEMA

/**
 * Gerar URL completa do site
 * 
 * @param string $path caminho (ex: 'clube-detalhe.php')
 * @return string URL completa (ex: '/vivedistrital/clube-detalhe.php')
 */
function url($path = '') {
    return BASE_URL . $path;
}

/**
 * Verificar se página atual está ativa (para menu de navegação)
 * 
 * @param string $page Nome do ficheiro (ex: 'index.php')
 * @return string 'active' se página atual, vazio caso contrário
 */
function isActive($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return ($current == $page) ? 'active' : '';
}

/**
 * Verificar se utilizador está autenticado
 * 
 * @return bool TRUE se utilizador fez login
 */
function isLoggedIn() { // ESTA FUNÇÃO APARECE EM: includes/topbar, includes/sidebar, login, registo, favoritos, admin/admin, admin/admin-utilizadores, admin/admin-classificacoes, config, ...
    return isset($_SESSION['user_id']);
}

/**
 * Obter dados completos do utilizador logado
 * @return array|null Dados do utilizador ou NULL se não autenticado
 */
function getLoggedUser() { // ESTA FUNÇÃO APARECE EM: includes/topbar, includes/sidebar, favoritos, admin/admin, config, ...
    global $pdo;
    
    // Verificar se existe sessão ativa
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        // Buscar dados do utilizador
        // LEFT JOIN para incluir o clube favorito também
        $stmt = $pdo->prepare("
            SELECT u.*, c.nome as clube_favorito_nome, c.codigo as clube_favorito_codigo, c.logo as clube_favorito_logo
            FROM utilizadores u
            LEFT JOIN clubes c ON u.clube_favorito_id = c.id
            WHERE u.id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Verificar se utilizador tem privilégios de administrador
 * @return bool TRUE se utilizador é admin
 */
function isAdmin() { // ESTA FUNÇÃO APARECE EM: includes/topbar, admin/admin, admin/admin-utilizadores, admin/admin-classificacoes, admin/admin-noticias, config, ...
    if (!isLoggedIn()) {
        return false;
    }
    $user = getLoggedUser();
    return $user && $user['is_admin'] == 1;
}

/**
 * Verificar se utilizador é jornalista
 * @return bool TRUE se utilizador é jornalista
 */
function isJornalista() { // ESTA FUNÇÃO APARECE EM: config (usada por canManageNoticias)
    if (!isLoggedIn()) {
        return false;
    }
    $user = getLoggedUser();
    return $user && ($user['is_jornalista'] ?? 0) == 1;
}

/**
 * FUNÇÃO DE SEGURANÇA: Escape HTML
 * - Sem e(): Executa JavaScript malicioso
 * - Com e(): Mostra texto literal seguro
 * 
 * @param string $string Texto para escapar
 * @return string Texto seguro para HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Função para verificar se pode gerir notícias (admin OU jornalista)
function canManageNoticias() { // ESTA FUNÇÃO APARECE EM: admin/admin-noticias, admin/admin-noticias-editar, noticias, config, ...
    return isAdmin() || isJornalista();
}

// Função para obter próximos jogos do clube favorito
function getProximosJogosClubeFavorito() { // ESTA FUNÇÃO APARECE EM: config (definida aqui; sem chamadas diretas no projeto neste momento)
    global $pdo; // global usado para acessar variável fora do escopo da função
    if (!isLoggedIn()) {
        return [];
    }
    
    $user = getLoggedUser();
    if (!$user || !$user['clube_favorito_id']) { // Se não tiver clube favorito definido
        return [];
    }
    
    try {
        // INNER JOIN para obter detalhes dos clubes
        // ASC na data para obter os mais próximos primeiro
        // Buscar jogos agendados do clube favorito
        $stmt = $pdo->prepare("
            SELECT j.*, 
                   cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
                   cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
            FROM jogos j
            INNER JOIN clubes cc ON j.clube_casa_id = cc.id
            INNER JOIN clubes cf ON j.clube_fora_id = cf.id
            WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?)
            AND j.status = 'agendado'
            AND j.data_jogo >= NOW()
            ORDER BY j.data_jogo ASC
            LIMIT 10
        ");
        $stmt->execute([$user['clube_favorito_id'], $user['clube_favorito_id']]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Função para obter resultados recentes do clube favorito
function getResultadosRecentesClubeFavorito() { // ESTA FUNÇÃO APARECE EM: config (definida aqui; sem chamadas diretas no projeto neste momento)
    global $pdo;
    if (!isLoggedIn()) {
        return [];
    }
    
    $user = getLoggedUser();
    if (!$user || !$user['clube_favorito_id']) {
        return [];
    }
    
    try {
        // INNER JOIN para obter detalhes dos clubes
        // DESC na data para obter os mais recentes primeiro
        // Buscar jogos finalizados do clube favorito
        $stmt = $pdo->prepare("
            SELECT j.*, 
                   cc.nome as clube_casa_nome, cc.logo as clube_casa_logo, cc.codigo as clube_casa_codigo,
                   cf.nome as clube_fora_nome, cf.logo as clube_fora_logo, cf.codigo as clube_fora_codigo
            FROM jogos j
            INNER JOIN clubes cc ON j.clube_casa_id = cc.id
            INNER JOIN clubes cf ON j.clube_fora_id = cf.id
            WHERE (j.clube_casa_id = ? OR j.clube_fora_id = ?)
            AND j.status = 'finalizado'
            ORDER BY j.data_jogo DESC
            LIMIT 10
        ");
        $stmt->execute([$user['clube_favorito_id'], $user['clube_favorito_id']]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Função para obter todas as notícias
// ESTA FUNÇÃO APARECE EM: admin/admin-noticias, noticias, config, ...
function getNoticias($limit = null) { // $limit é o número máximo de notícias a retornar
    global $pdo;
    try {
        // Buscar notícias com autor
        // ORDER BY data_publicacao DESC para as mais recentes primeiro
        // INNER JOIN para obter nome do autor
        $sql = "
            SELECT n.*, u.username as autor_nome 
            FROM noticias n
            INNER JOIN utilizadores u ON n.autor_id = u.id
            ORDER BY n.data_publicacao DESC
        ";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Função para obter uma notícia por ID
function getNoticiaById($id) { // ESTA FUNÇÃO APARECE EM: config (definida aqui; sem chamadas diretas no projeto neste momento)
    global $pdo;
    try {
        // Buscar noticias através do ID fornecido
        // INNER JOIN para associar o nome do autor
        // WHERE para filtrar pela notícia específica
        $stmt = $pdo->prepare("
            SELECT n.*, u.username as autor_nome 
            FROM noticias n
            INNER JOIN utilizadores u ON n.autor_id = u.id
            WHERE n.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
?>
