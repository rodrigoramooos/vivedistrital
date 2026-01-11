-- Criar base de dados
DROP DATABASE IF EXISTS vivedistrital;
CREATE DATABASE vivedistrital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vivedistrital;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Tabela de clubes
CREATE TABLE clubes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de utilizadores
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    clube_favorito_id INT,
    is_admin TINYINT(1) DEFAULT 0,
    is_jornalista TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (clube_favorito_id) REFERENCES clubes(id) ON DELETE SET NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de notificacoes
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT,
    tipo VARCHAR(50) DEFAULT 'info',
    lida TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    INDEX idx_utilizador (utilizador_id),
    INDEX idx_lida (lida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de jogos
CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clube_casa_id INT NOT NULL,
    clube_fora_id INT NOT NULL,
    resultado_casa INT NULL,
    resultado_fora INT NULL,
    data_jogo DATETIME NOT NULL,
    jornada INT NOT NULL,
    status VARCHAR(20) DEFAULT 'agendado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clube_casa_id) REFERENCES clubes(id) ON DELETE CASCADE,
    FOREIGN KEY (clube_fora_id) REFERENCES clubes(id) ON DELETE CASCADE,
    INDEX idx_data_jogo (data_jogo),
    INDEX idx_jornada (jornada),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de classificacoes
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

-- Tabela de noticias
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

-- Inserir clubes
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

-- Inserir utilizadores
INSERT INTO utilizadores (username, password, email, is_admin, is_jornalista) VALUES
('admin', 'admin', 'admin@vivedistrital.pt', 1, 1);

INSERT INTO utilizadores (username, password, email, clube_favorito_id) VALUES
('user', 'user', 'user@vivedistrital.pt', 1);

-- Inserir classificacoes
INSERT INTO classificacoes (clube_id, posicao, jogos, vitorias, empates, derrotas, golos_marcados, golos_sofridos, diferenca_golos, pontos, forma) VALUES
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), 1, 5, 5, 0, 0, 13, 1, 12, 15, 'V V V V V'),
((SELECT id FROM clubes WHERE codigo = 'tocha'), 2, 5, 4, 1, 0, 12, 2, 10, 13, 'V V E V V'),
((SELECT id FROM clubes WHERE codigo = 'uniaocoimbra'), 3, 4, 3, 1, 0, 14, 0, 14, 10, 'V E V V'),
((SELECT id FROM clubes WHERE codigo = 'sourense'), 4, 5, 3, 0, 2, 10, 5, 5, 9, 'V D V V D'),
((SELECT id FROM clubes WHERE codigo = 'vigor'), 5, 5, 3, 0, 2, 9, 8, 1, 9, 'D V V D V'),
((SELECT id FROM clubes WHERE codigo = 'academica'), 6, 5, 2, 1, 2, 8, 7, 1, 7, 'V E D D V'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), 7, 5, 2, 1, 2, 7, 8, -1, 7, 'D V E D V'),
((SELECT id FROM clubes WHERE codigo = 'tourizense'), 8, 5, 1, 3, 1, 3, 4, -1, 6, 'E D E E V'),
((SELECT id FROM clubes WHERE codigo = 'anca'), 9, 5, 2, 0, 3, 6, 9, -3, 6, 'V D D V D'),
((SELECT id FROM clubes WHERE codigo = 'carapinheirense'), 10, 5, 2, 0, 3, 5, 10, -5, 6, 'D V D V D'),
((SELECT id FROM clubes WHERE codigo = 'eirense'), 11, 5, 1, 2, 2, 1, 6, -5, 5, 'E D E D V'),
((SELECT id FROM clubes WHERE codigo = 'pedrulhense'), 12, 5, 1, 2, 2, 10, 15, -5, 5, 'D E V E D'),
((SELECT id FROM clubes WHERE codigo = 'penelense'), 13, 5, 1, 1, 3, 5, 8, -3, 4, 'D V E D D'),
((SELECT id FROM clubes WHERE codigo = 'uniao'), 14, 5, 0, 3, 2, 3, 6, -3, 3, 'E D E E D'),
((SELECT id FROM clubes WHERE codigo = 'mocidade'), 15, 5, 0, 1, 4, 0, 7, -7, 1, 'D D E D D'),
((SELECT id FROM clubes WHERE codigo = 'poiares'), 16, 4, 0, 0, 4, 3, 9, -6, 0, 'D D D D');

-- Inserir jogos jornada 8
INSERT INTO jogos (clube_casa_id, clube_fora_id, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), (SELECT id FROM clubes WHERE codigo = 'academica'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'vigor'), (SELECT id FROM clubes WHERE codigo = 'uniao'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'anca'), (SELECT id FROM clubes WHERE codigo = 'pedrulhense'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'carapinheirense'), (SELECT id FROM clubes WHERE codigo = 'penelense'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'eirense'), (SELECT id FROM clubes WHERE codigo = 'poiares'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), (SELECT id FROM clubes WHERE codigo = 'mocidade'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'sourense'), (SELECT id FROM clubes WHERE codigo = 'uniao'), '2025-11-23 15:00:00', 8, 'agendado'),
((SELECT id FROM clubes WHERE codigo = 'tocha'), (SELECT id FROM clubes WHERE codigo = 'tourizense'), '2025-11-23 15:00:00', 8, 'agendado');

-- Inserir jogos jornada 7
INSERT INTO jogos (clube_casa_id, clube_fora_id, resultado_casa, resultado_fora, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'academica'), (SELECT id FROM clubes WHERE codigo = 'vigor'), 2, 1, '2025-11-16 15:00:00', 7, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), (SELECT id FROM clubes WHERE codigo = 'carapinheirense'), 3, 0, '2025-11-16 15:00:00', 7, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'pedrulhense'), (SELECT id FROM clubes WHERE codigo = 'eirense'), 1, 1, '2025-11-16 15:00:00', 7, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'penelense'), (SELECT id FROM clubes WHERE codigo = 'anca'), 2, 2, '2025-11-16 15:00:00', 7, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'poiares'), (SELECT id FROM clubes WHERE codigo = 'esperanca'), 0, 1, '2025-11-16 15:00:00', 7, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'uniao'), (SELECT id FROM clubes WHERE codigo = 'mocidade'), 3, 2, '2025-11-16 15:00:00', 7, 'finalizado');

-- Inserir jogos jornada 6
INSERT INTO jogos (clube_casa_id, clube_fora_id, resultado_casa, resultado_fora, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'vigor'), (SELECT id FROM clubes WHERE codigo = 'nogueirense'), 1, 2, '2025-11-09 15:00:00', 6, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'academica'), (SELECT id FROM clubes WHERE codigo = 'pedrulhense'), 4, 0, '2025-11-09 15:00:00', 6, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'carapinheirense'), (SELECT id FROM clubes WHERE codigo = 'anca'), 1, 1, '2025-11-09 15:00:00', 6, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'eirense'), (SELECT id FROM clubes WHERE codigo = 'penelense'), 2, 3, '2025-11-09 15:00:00', 6, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), (SELECT id FROM clubes WHERE codigo = 'poiares'), 1, 0, '2025-11-09 15:00:00', 6, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'mocidade'), (SELECT id FROM clubes WHERE codigo = 'uniao'), 0, 2, '2025-11-09 15:00:00', 6, 'finalizado');

-- Inserir jogos jornada 5
INSERT INTO jogos (clube_casa_id, clube_fora_id, resultado_casa, resultado_fora, data_jogo, jornada, status) VALUES
((SELECT id FROM clubes WHERE codigo = 'sourense'), (SELECT id FROM clubes WHERE codigo = 'pedrulhense'), 5, 1, '2025-11-09 15:00:00', 5, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'poiares'), (SELECT id FROM clubes WHERE codigo = 'uniao'), 2, 1, '2025-11-09 15:00:00', 5, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'esperanca'), (SELECT id FROM clubes WHERE codigo = 'tourizense'), 1, 3, '2025-11-09 15:00:00', 5, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'anca'), (SELECT id FROM clubes WHERE codigo = 'academica'), 1, 0, '2025-11-09 15:00:00', 5, 'finalizado'),
((SELECT id FROM clubes WHERE codigo = 'nogueirense'), (SELECT id FROM clubes WHERE codigo = 'uniaocoimbra'), 0, 0, '2025-11-09 15:00:00', 5, 'finalizado');

-- Inserir noticias
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

-- Inserir notificacoes
INSERT INTO notificacoes (utilizador_id, titulo, mensagem, tipo) VALUES
(2, 'Bem-vindo ao Vive Distrital!', 'Obrigado por se registar na nossa plataforma.', 'success'),
(2, 'Novo jogo do seu clube favorito', 'A Académica SF joga em breve. Consulte o calendário.', 'info'),
(2, 'Atualização de classificações', 'As classificações foram atualizadas após a última jornada.', 'warning');

SELECT 'Base de dados criada!' as status;
SELECT COUNT(*) as total_clubes FROM clubes;
SELECT COUNT(*) as total_utilizadores FROM utilizadores;
SELECT COUNT(*) as total_jogos FROM jogos;
SELECT COUNT(*) as total_noticias FROM noticias;
SELECT COUNT(*) as total_classificacoes FROM classificacoes;