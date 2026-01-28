-- VIVE DISTRITAL - Base de Dados Completa

-- Remover e criar base de dados
DROP DATABASE IF EXISTS vivedistrital;
CREATE DATABASE vivedistrital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; -- utf8mb4 para suportar todos os caracteres, incluindo emojis
USE vivedistrital;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- TABELA: clubes
-- Armazena informação dos clubes participantes
CREATE TABLE clubes (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único do clube
    nome VARCHAR(100) NOT NULL, -- varchar é usado para strings de tamanho variável
    codigo VARCHAR(50) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT 'imgs/equipas/default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação do registo
    INDEX idx_codigo (codigo) -- Índice para pesquisa rápida pelo código do clube
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; -- InnoDB para suportar chaves estrangeiras

-- TABELA: utilizadores
-- Gestão de utilizadores do sistema
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    clube_favorito_id INT,
    is_admin TINYINT(1) DEFAULT 0, -- TINYINT é usado para booleanos (0 ou 1), que são os valores mais pequenos possíveis
    is_jornalista TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação do registo
    last_login TIMESTAMP NULL, -- Último login do utilizador
    FOREIGN KEY (clube_favorito_id) REFERENCES clubes(id) ON DELETE SET NULL, -- Se o clube for apagado, o clube_favorito_id fica NULL
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABELA: jogos
-- Registo de jogos do campeonato
CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clube_casa_id INT NOT NULL,
    clube_fora_id INT NOT NULL,
    resultado_casa INT NULL,
    resultado_fora INT NULL,
    data_jogo DATETIME NOT NULL,
    jornada INT NOT NULL,
    status VARCHAR(20) DEFAULT 'agendado',
    destaque TINYINT(1) DEFAULT 0 COMMENT 'Indica se o jogo está em destaque (apenas um pode estar ativo)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clube_casa_id) REFERENCES clubes(id) ON DELETE CASCADE,
    FOREIGN KEY (clube_fora_id) REFERENCES clubes(id) ON DELETE CASCADE,
    INDEX idx_data_jogo (data_jogo),
    INDEX idx_jornada (jornada),
    INDEX idx_status (status),
    INDEX idx_destaque (destaque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABELA: classificacoes
-- Classificação atualizada dos clubes
CREATE TABLE classificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clube_id INT NOT NULL,
    posicao INT NOT NULL,
    jogos INT DEFAULT 0,
    vitorias INT DEFAULT 0,
    empates INT DEFAULT 0,
    derrotas INT DEFAULT 0,
    golos_marcados INT DEFAULT 0,
    golos_sofridos INT DEFAULT 0,
    diferenca_golos INT DEFAULT 0,
    pontos INT DEFAULT 0,
    forma VARCHAR(50) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_clube (clube_id),
    FOREIGN KEY (clube_id) REFERENCES clubes(id) ON DELETE CASCADE,
    INDEX idx_posicao (posicao),
    INDEX idx_pontos (pontos DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABELA: noticias
-- Sistema de notícias gerido por admins/jornalistas
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    resumo TEXT NOT NULL,
    conteudo TEXT NOT NULL,
    categoria VARCHAR(50) DEFAULT 'fire',
    imagem VARCHAR(255) DEFAULT NULL,
    autor_id INT NOT NULL,
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (autor_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    INDEX idx_data_publicacao (data_publicacao DESC),
    INDEX idx_autor (autor_id),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DADOS INICIAIS: Clubes
INSERT INTO clubes (nome, codigo, logo) VALUES
('Académica SF', 'academica', 'imgs/equipas/aac-sf.png'),
('Ançã', 'anca', 'imgs/equipas/anca.png'),
('Carapinheirense', 'carapinheirense', 'imgs/equipas/carapinheirense.png'),
('Eirense', 'eirense', 'imgs/equipas/eirense.png'),
('Esperança', 'esperanca', 'imgs/equipas/esperanca.png'),
('Mocidade', 'mocidade', 'imgs/equipas/mocidade.png'),
('Nogueirense', 'nogueirense', 'imgs/equipas/nogueirense.png'),
('Pedrulhense', 'pedrulhense', 'imgs/equipas/pedrulhense.png'),
('Penelense', 'penelense', 'imgs/equipas/penelense.png'),
('Poiares', 'poiares', 'imgs/equipas/poiares.png'),
('Sourense', 'sourense', 'imgs/equipas/sourense.png'),
('Tocha', 'tocha', 'imgs/equipas/tocha.png'),
('Tourizense', 'tourizense', 'imgs/equipas/tourizense.png'),
('União 1919', 'uniaocoimbra', 'imgs/equipas/uniaocoimbra.png'),
('Vigor Mocidade', 'vigor', 'imgs/equipas/vigor.png'),
('União FC', 'uniao', 'imgs/equipas/uniao.png');

-- DADOS INICIAIS: Utilizadores
-- Utilizador Admin (username: admin, password: admin)
INSERT INTO utilizadores (username, password, email, is_admin, is_jornalista) VALUES
('admin', 'admin', 'admin@vivedistrital.pt', 1, 1);

-- Utilizador Teste (username: user, password: user)
INSERT INTO utilizadores (username, password, email, clube_favorito_id) VALUES
('user', 'user', 'user@vivedistrital.pt', 1);

-- DADOS INICIAIS: Jogos Finalizados (Jornada 1)
-- 8 jogos - Todos os clubes jogam entre si
INSERT INTO jogos (clube_casa_id, clube_fora_id, resultado_casa, resultado_fora, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), (SELECT id FROM clubes WHERE codigo = 'carapinheirense'), 3, 0, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'tocha'), (SELECT id FROM clubes WHERE codigo = 'eirense'), 2, 1, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'uniaocoimbra'), (SELECT id FROM clubes WHERE codigo = 'esperanca'), 4, 1, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'sourense'), (SELECT id FROM clubes WHERE codigo = 'mocidade'), 2, 0, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'vigor'), (SELECT id FROM clubes WHERE codigo = 'pedrulhense'), 1, 1, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'academica'), (SELECT id FROM clubes WHERE codigo = 'tourizense'), 2, 1, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'anca'), (SELECT id FROM clubes WHERE codigo = 'uniao'), 3, 2, '2026-01-18 15:00:00', 1, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'penelense'), (SELECT id FROM clubes WHERE codigo = 'poiares'), 1, 0, '2026-01-18 15:00:00', 1, 'finalizado');

-- DADOS INICIAIS: Próximos Jogos (Jornada 2)
-- 5 jogos agendados
INSERT INTO jogos (clube_casa_id, clube_fora_id, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'carapinheirense'), (SELECT id FROM clubes WHERE codigo = 'tocha'), '2026-02-01 15:00:00', 2, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'eirense'), (SELECT id FROM clubes WHERE codigo = 'uniaocoimbra'), '2026-02-01 15:00:00', 2, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), (SELECT id FROM clubes WHERE codigo = 'sourense'), '2026-02-01 15:00:00', 2, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'mocidade'), (SELECT id FROM clubes WHERE codigo = 'vigor'), '2026-02-01 15:00:00', 2, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'pedrulhense'), (SELECT id FROM clubes WHERE codigo = 'academica'), '2026-02-01 15:00:00', 2, 'agendado');

-- DADOS INICIAIS: Classificações (Calculadas dos jogos acima)
INSERT INTO classificacoes (clube_id, posicao, jogos, vitorias, empates, derrotas, golos_marcados, golos_sofridos, diferenca_golos, pontos, forma) VALUES
((SELECT id FROM clubes WHERE codigo = 'uniaocoimbra'), 1, 1, 1, 0, 0, 4, 1, 3, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), 2, 1, 1, 0, 0, 3, 0, 3, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'anca'), 3, 1, 1, 0, 0, 3, 2, 1, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'sourense'), 4, 1, 1, 0, 0, 2, 0, 2, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'tocha'), 5, 1, 1, 0, 0, 2, 1, 1, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'academica'), 6, 1, 1, 0, 0, 2, 1, 1, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'penelense'), 7, 1, 1, 0, 0, 1, 0, 1, 3, 'V'),
((SELECT id FROM clubes WHERE codigo = 'uniao'), 8, 1, 0, 0, 1, 2, 3, -1, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'vigor'), 9, 1, 0, 1, 0, 1, 1, 0, 1, 'E'),
((SELECT id FROM clubes WHERE codigo = 'pedrulhense'), 10, 1, 0, 1, 0, 1, 1, 0, 1, 'E'),
((SELECT id FROM clubes WHERE codigo = 'eirense'), 11, 1, 0, 0, 1, 1, 2, -1, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), 12, 1, 0, 0, 1, 1, 4, -3, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'tourizense'), 13, 1, 0, 0, 1, 1, 2, -1, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'poiares'), 14, 1, 0, 0, 1, 0, 1, -1, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'mocidade'), 15, 1, 0, 0, 1, 0, 2, -2, 0, 'D'),
((SELECT id FROM clubes WHERE codigo = 'carapinheirense'), 16, 1, 0, 0, 1, 0, 3, -3, 0, 'D');

-- DADOS INICIAIS: Notícias
INSERT INTO noticias (titulo, resumo, conteudo, categoria, autor_id, data_publicacao) VALUES
('Tocha (em brasa) goleia Eirense', 
 'O Tocha dominou completamente o jogo e conquistou uma vitória expressiva por 5-0 contra o Eirense.', 
 'O Tocha apresentou um futebol brilhante e conquistou uma vitória expressiva por 5-0 contra o Eirense. A equipa da casa dominou desde o primeiro minuto, com uma exibição coletiva de grande nível.\n\nOs golos surgiram ao longo de todo o jogo, demonstrando a superioridade técnica e tática do Tocha. O público presente no estádio assistiu a um verdadeiro espetáculo de futebol.\n\nCom este resultado, o Tocha continua a sua caminhada positiva na competição e consolida a sua posição na tabela classificativa.',
 'fire', 1, '2025-11-17 14:30:00'),

('União 1919 e Nogueirense mantêm a sua invencibilidade', 
 'As duas equipas continuam sem conhecer a derrota na presente edição do campeonato.', 
 'A União 1919 e o Nogueirense continuam a demonstrar grande consistência nesta temporada, mantendo-se invictos após mais uma jornada disputada.\n\nAmbas as equipas têm apresentado um futebol sólido, com defesas bem organizadas e ataques eficazes. A União 1919 conquistou mais uma vitória convincente, enquanto o Nogueirense garantiu um importante empate fora de casa.\n\nEsta invencibilidade coloca ambas as equipas na corrida pelos primeiros lugares da classificação, prometendo uma luta intensa até ao final da temporada.',
 'shield', 1, '2025-11-17 10:15:00'),

('Academistas regressam às vitórias', 
 'A Académica SF conseguiu uma importante vitória após uma série de resultados menos positivos.', 
 'Depois de alguns jogos sem vencer, a Académica SF voltou aos triunfos com uma exibição segura e convincente. A equipa mostrou-se mais organizada e eficaz nas suas investidas ofensivas.\n\nO treinador fez alguns ajustes táticos que surtiram efeito imediato, com a equipa a demonstrar maior coesão e confiança em campo. Os adeptos presentes no estádio celebraram com entusiasmo esta importante vitória.\n\nCom este resultado, a Académica SF procura agora encadear uma série positiva de resultados e subir na tabela classificativa.',
 'trophy', 1, '2025-11-17 09:00:00'),

('Sourense soma mais uma vitória convincente', 
 'O Sourense continua a surpreender com exibições de grande qualidade e conquistou mais três pontos.', 
 'O Sourense tem sido uma das revelações desta temporada e voltou a demonstrar a sua qualidade com mais uma vitória convincente. A equipa jogou com grande confiança e dominou o adversário durante grande parte do encontro.\n\nOs golos surgiram de jogadas bem trabalhadas, evidenciando o bom trabalho coletivo da equipa. O técnico do Sourense tem conseguido tirar o melhor rendimento do plantel disponível.\n\nCom estas exibições, o Sourense consolida-se como uma das equipas mais fortes da competição e candidata a lugares cimeiros na classificação final.',
 'trophy', 1, '2025-11-16 16:45:00'),

('Análise: O meio da tabela está ao rubro', 
 'A luta pela manutenção e por melhores posições está extremamente competitiva nesta temporada.', 
 'O meio da tabela classificativa apresenta uma competitividade extrema, com várias equipas separadas por poucos pontos. Cada jornada pode alterar significativamente as posições e as perspetivas de cada clube.\n\nAs equipas que ocupam estas posições têm alternado entre vitórias e derrotas, tornando impossível prever qual será a classificação final. Vários clubes tradicionais encontram-se nesta zona de turbulência.\n\nOs próximos jogos serão decisivos para definir quais as equipas que conseguirão ascender a posições mais tranquilas e quais terão de lutar pela permanência até às últimas jornadas.',
 'emoji_events', 1, '2025-11-15 11:20:00');

-- Verificação final
SELECT 'Base de dados criada com sucesso!' as status;
SELECT COUNT(*) as total_clubes FROM clubes;
SELECT COUNT(*) as total_utilizadores FROM utilizadores;
SELECT COUNT(*) as total_jogos FROM jogos;
SELECT COUNT(*) as total_noticias FROM noticias;
SELECT COUNT(*) as total_classificacoes FROM classificacoes;