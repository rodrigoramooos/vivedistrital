<?php
// Importar configuração existente
require_once __DIR__ . '/includes/config.php';

/**
 * Obter ligação PDO da configuração principal
 * @return PDO
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * FUNÇÃO: Calcular Estatísticas de um Clube
 *
 * 
 * Calcula TODAS as estatísticas dinamicamente a partir dos jogos finalizados
 * 
 * @param int $clube_id
 * @return array Estatísticas completas do clube
 */
function calcularEstatisticasClube($clube_id) {
    $pdo = getDB();
    
    // Buscar todos os jogos finalizados do clube
    // where clube é casa ou fora
    // not null para garantir que só considera jogos com resultados
    // desc para começar pelos mais recentes
    $stmt = $pdo->prepare("
        SELECT 
            clube_casa_id,
            clube_fora_id,
            resultado_casa,
            resultado_fora,
            data_jogo
        FROM jogos
        WHERE (clube_casa_id = :clube_id_casa OR clube_fora_id = :clube_id_fora)
          AND resultado_casa IS NOT NULL
          AND resultado_fora IS NOT NULL
          AND status = 'finalizado'
        ORDER BY data_jogo DESC
    ");
    $stmt->execute([ // => $clube_id é feito para que o mesmo valor seja usado em dois parâmetros
        ':clube_id_casa' => $clube_id,
        ':clube_id_fora' => $clube_id
    ]);
    $jogos = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch_assoc() que obtém a próxima linha de um conjunto de resultados MySQL como um array associativo
    
    // Inicializar estatísticas
    $stats = [
        'jogos' => 0,
        'vitorias' => 0,
        'empates' => 0,
        'derrotas' => 0,
        'pontos' => 0,
        'golos_marcados' => 0,
        'golos_sofridos' => 0,
        'diferenca_golos' => 0,
        'forma' => ''
    ];
    
    $forma_array = [];
    
    foreach ($jogos as $jogo) {
        $stats['jogos']++;
        
        // Determinar se jogou em casa ou fora
        $em_casa = ($jogo['clube_casa_id'] == $clube_id);
        $golos_pro = $em_casa ? (int)$jogo['resultado_casa'] : (int)$jogo['resultado_fora']; // golos_pro são os golos marcados pelo clube; (int) para garantir que é um inteiro
        $golos_contra = $em_casa ? (int)$jogo['resultado_fora'] : (int)$jogo['resultado_casa']; // golos_contra são os golos sofridos pelo clube
        
        $stats['golos_marcados'] += $golos_pro;
        $stats['golos_sofridos'] += $golos_contra;
        
        // Determinar resultado
        if ($golos_pro > $golos_contra) { // se marcou mais golos do que sofreu
            $stats['vitorias']++; // adiciona uma vitória
            $stats['pontos'] += 3; // 3 pontos por vitória
            $forma_array[] = 'V'; // adiciona 'V' à forma
        } elseif ($golos_pro < $golos_contra) { // se marcou menos golos do que sofreu
            $stats['derrotas']++; // adiciona uma derrota e não adiciona pontos
            $forma_array[] = 'D'; // adiciona 'D' à forma
        } else {
            $stats['empates']++; // adiciona um empate
            $stats['pontos'] += 1; // 1 ponto por empate
            $forma_array[] = 'E'; // adiciona 'E' à forma
        }
    }
    
    $stats['diferenca_golos'] = $stats['golos_marcados'] - $stats['golos_sofridos'];
    $stats['forma'] = implode('', array_slice($forma_array, 0, 5)); // implode é usado para juntar os elementos do array em uma string, array_slice limita a 5 últimos jogos
    
    // array slice ≠ array "normal" porque o slice apenas busca os primeiros 5 elementos do array e o array "normal" buscaria todos os elementos

    return $stats;
}

/**

 * Atualizar Estatísticas de um Clube

 * 
 * @param int $clube_id
 * @return bool
 */
function atualizarEstatisticasClube($clube_id) {
    $pdo = getDB();
    $stats = calcularEstatisticasClube($clube_id); // obter estatísticas atualizadas
    
    // Verificar se já existe
    $stmt = $pdo->prepare("SELECT id FROM classificacoes WHERE clube_id = :clube_id"); // :clube_id porque é um parâmetro nomeado (um marcador de posição que é substituído por um valor real quando a consulta é executada)
    $stmt->execute([':clube_id' => $clube_id]);
    $existe = $stmt->fetch();
    
    if ($existe) {
        // UPDATE: Atualizar estatísticas recalculadas do clube existente
        $sql = "UPDATE classificacoes 
                SET jogos = :jogos,
                    pontos = :pontos,
                    vitorias = :vitorias,
                    empates = :empates,
                    derrotas = :derrotas,
                    golos_marcados = :golos_marcados,
                    golos_sofridos = :golos_sofridos,
                    diferenca_golos = :diferenca_golos,
                    forma = :forma
                WHERE clube_id = :clube_id";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':jogos' => $stats['jogos'],
            ':pontos' => $stats['pontos'],
            ':vitorias' => $stats['vitorias'],
            ':empates' => $stats['empates'],
            ':derrotas' => $stats['derrotas'],
            ':golos_marcados' => $stats['golos_marcados'],
            ':golos_sofridos' => $stats['golos_sofridos'],
            ':diferenca_golos' => $stats['diferenca_golos'],
            ':forma' => $stats['forma'],
            ':clube_id' => $clube_id
        ]);
    } else {
        // INSERT
        $sql = "INSERT INTO classificacoes 
                    (clube_id, jogos, pontos, vitorias, empates, derrotas, 
                     golos_marcados, golos_sofridos, diferenca_golos, forma, posicao)
                VALUES 
                    (:clube_id, :jogos, :pontos, :vitorias, :empates, :derrotas, 
                     :golos_marcados, :golos_sofridos, :diferenca_golos, :forma, 0)";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':clube_id' => $clube_id,
            ':jogos' => $stats['jogos'],
            ':pontos' => $stats['pontos'],
            ':vitorias' => $stats['vitorias'],
            ':empates' => $stats['empates'],
            ':derrotas' => $stats['derrotas'],
            ':golos_marcados' => $stats['golos_marcados'],
            ':golos_sofridos' => $stats['golos_sofridos'],
            ':diferenca_golos' => $stats['diferenca_golos'],
            ':forma' => $stats['forma']
        ]);
    }
}

/**

 * Recalcular Posições de Todos os Clubes

 * 
 * Atualiza o campo 'posicao' baseado nos critérios de desempate:
 * 1. Pontos (DESC)
 * 2. Diferença de golos (DESC)
 * 3. Vitórias (DESC)
 * 4. Golos marcados (DESC)
 */

// DESC porque queremos do maior para o menor
function recalcularPosicoes() {
    $pdo = getDB();
    
    $clubes = $pdo->query("
        SELECT clube_id 
        FROM classificacoes 
        ORDER BY pontos DESC, 
                 diferenca_golos DESC, 
                 vitorias DESC, 
                 golos_marcados DESC
    ")->fetchAll(PDO::FETCH_COLUMN); // FETCH_COLUMN para obter apenas a primeira coluna (clube_id)
    
    $posicao = 1; // iniciar a posição em 1
    $stmt = $pdo->prepare("UPDATE classificacoes SET posicao = :pos WHERE clube_id = :id"); // :pos é a posição atual, :id é o clube_id
    
    foreach ($clubes as $clube_id) {
        $stmt->execute([':pos' => $posicao, ':id' => $clube_id]); // executar a atualização
        $posicao++; // incrementar a posição para o próximo clube
    }
}

/**
 * Obter todos os clubes ordenados por classificação
 * @return array
 */

// COALESCE é usado para garantir que valores nulos sejam tratados como 0, retornando o primeiro valor não nulo
// o COALESCE é necessário porque alguns clubes podem não ter entradas na tabela classificações
// cl.forma as forma é usado para obter a forma do clube
// LEFT JOIN ao invés de INNER JOIN para garantir que todos os clubes sejam retornados, mesmo que não tenham estatísticas
// cl.posicao, 999 é usado para garantir que clubes sem posição apareçam no final da lista, o 999 porque é um valor alto que não será alcançado
function getClubes() {
    $pdo = getDB();
    $sql = "SELECT c.*, 
                   COALESCE(cl.pontos, 0) as pontos,
                   COALESCE(cl.jogos, 0) as jogos,
                   COALESCE(cl.vitorias, 0) as vitorias,
                   COALESCE(cl.empates, 0) as empates,
                   COALESCE(cl.derrotas, 0) as derrotas,
                   COALESCE(cl.golos_marcados, 0) as golos_marcados,
                   COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
                   COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
                   COALESCE(cl.posicao, 999) as posicao,
                   cl.forma as forma
            FROM clubes c
            LEFT JOIN classificacoes cl ON c.id = cl.clube_id
            ORDER BY COALESCE(cl.pontos, 0) DESC, 
                     COALESCE(cl.diferenca_golos, 0) DESC, 
                     COALESCE(cl.vitorias, 0) DESC, 
                     COALESCE(cl.golos_marcados, 0) DESC";
    return $pdo->query($sql)->fetchAll(); // fetchAll() para obter todos os resultados como um array
}


// getClubes() retorna todos os clubes ordenados por classificação: 1º - Pontos (DESC), 2º - Diferença de golos (DESC), 3º - Vitórias (DESC), 4º - Golos marcados (DESC), uso para tabelas de classificação
// getClubeByCodigo() retorna um clube específico pelo seu código único, filtrado por WHERE c.codigo = :codigo + LIMIT 1, uso para pagina de detalhes do clube e favoritos
// getClubeById() retorna um clube específico pelo seu ID, filtrado por WHERE c.id = :id + LIMIT 1, uso para editar clubes e favoritos


/**
 * Obter clube por código
 * @param string $codigo
 * @return array|false
 */
function getClubeByCodigo($codigo) { // $codigo é o código único do clube
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COALESCE(cl.pontos, 0) as pontos,
               COALESCE(cl.jogos, 0) as jogos,
               COALESCE(cl.vitorias, 0) as vitorias,
               COALESCE(cl.empates, 0) as empates,
               COALESCE(cl.derrotas, 0) as derrotas,
               COALESCE(cl.golos_marcados, 0) as golos_marcados,
               COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
               COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
               COALESCE(cl.posicao, 999) as posicao,
               cl.forma as forma
        FROM clubes c
        LEFT JOIN classificacoes cl ON c.id = cl.clube_id
        WHERE c.codigo = :codigo
        LIMIT 1
    ");
    $stmt->execute([':codigo' => $codigo]);
    return $stmt->fetch();
}

/**
 * Obter clube por ID
 * @param int $id
 * @return array|false
 */
function getClubeById($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COALESCE(cl.pontos, 0) as pontos,
               COALESCE(cl.jogos, 0) as jogos,
               COALESCE(cl.vitorias, 0) as vitorias,
               COALESCE(cl.empates, 0) as empates,
               COALESCE(cl.derrotas, 0) as derrotas,
               COALESCE(cl.golos_marcados, 0) as golos_marcados,
               COALESCE(cl.golos_sofridos, 0) as golos_sofridos,
               COALESCE(cl.diferenca_golos, 0) as diferenca_golos,
               COALESCE(cl.posicao, 999) as posicao,
               cl.forma as forma
        FROM clubes c
        LEFT JOIN classificacoes cl ON c.id = cl.clube_id
        WHERE c.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Atualizar dados de um clube
 * @param int $id
 * @param array $dados
 * @return bool
 */

 // atualizarClube() atualiza os dados do clube na tabela clubes e as estatísticas na tabela classificações

function atualizarClube($id, $dados) {
    $pdo = getDB();
    
    // Atualizar logo na tabela clubes se fornecido
    if (isset($dados['logo'])) {
        $stmt = $pdo->prepare("UPDATE clubes SET logo = ? WHERE id = ?"); // o logo é atualizado apenas se fornecido
        $stmt->execute([$dados['logo'], $id]);
    }
    
    // Atualizar estatísticas na tabela classificacoes
    // VALUES tem um 0 em (?, 0, ...) para a posicao que será recalculada posteriormente
    $sql = "INSERT INTO classificacoes 
                (clube_id, posicao, jogos, pontos, vitorias, empates, derrotas, 
                 golos_marcados, golos_sofridos, diferenca_golos, forma)
            VALUES (?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                jogos = VALUES(jogos),
                pontos = VALUES(pontos),
                vitorias = VALUES(vitorias),
                empates = VALUES(empates),
                derrotas = VALUES(derrotas),
                golos_marcados = VALUES(golos_marcados),
                golos_sofridos = VALUES(golos_sofridos),
                diferenca_golos = VALUES(diferenca_golos),
                forma = VALUES(forma)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $id,
        $dados['jogos'] ?? 0,
        $dados['pontos'] ?? 0,
        $dados['vitorias'] ?? 0,
        $dados['empates'] ?? 0,
        $dados['derrotas'] ?? 0,
        $dados['golos_marcados'] ?? 0,
        $dados['golos_sofridos'] ?? 0,
        ($dados['golos_marcados'] ?? 0) - ($dados['golos_sofridos'] ?? 0),
        $dados['forma'] ?? null
    ]);
}

/**
 * Calcular posição do clube
 * @param string $codigo
 * @return int
 */

 // getPosicao() retorna a posição atual do clube baseado no seu código único

function getPosicao($codigo) {
    $clubes = getClubes();
    foreach ($clubes as $index => $clube) { // index é a posição no array (0-based)
        if ($clube['codigo'] === $codigo) {
            return $index + 1; // +1 para converter para 1-based (1ª posição é índice 0)
        }
    }
    return 0;
}

/**

 * Formatar Forma para HTML

 */

 // formatarForma() converte a string de forma (ex: "VVEDD") em HTML com classes CSS para estilização

function formatarForma($forma) {
    if (empty($forma)) {
        return '<div class="forma"><span class="forma-jogo nao-jogado">-</span></div>'; // Retorna um traço se a forma estiver vazia
    }
    
    $html = '<div class="forma">';
    
    // Remover espaços e separar por caractere
    $forma_limpa = str_replace(' ', '', $forma);
    $caracteres = str_split($forma_limpa);
    
    foreach ($caracteres as $char) { // $char é cada caractere individual da forma
        $char = strtoupper(trim($char));
        
        // Ignorar caracteres vazios
        if (empty($char)) continue;
        
        // Usar switch em vez de match porque match não é suportado em versões mais antigas do PHP
        switch ($char) {
            case 'V':
                $classe = 'vitoria';
                break;
            case 'E':
                $classe = 'empate';
                break;
            case 'D':
                $classe = 'derrota';
                break;
            default:
                continue 2; // Pular caracteres inválidos, 2 porque estamos dentro de um switch dentro de um foreach
        }
        
        $html .= "<span class='forma-jogo $classe'>$char</span>";
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Formatar diferença de golos
 * @param int $diferenca
 * @return string
 */

  // formatarDG() formata a diferença de golos com sinal positivo ou negativo, através de um + ou - antes do número

function formatarDG($diferenca) {
    return ($diferenca > 0 ? '+' : '') . $diferenca; // $diferenca é o valor da diferença de golos
}
?>
