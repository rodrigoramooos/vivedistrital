-- Adicionar campo is_jornalista à tabela utilizadores
ALTER TABLE utilizadores ADD COLUMN is_jornalista TINYINT(1) DEFAULT 0 AFTER is_admin;

-- Criar tabela de notícias
CREATE TABLE IF NOT EXISTS noticias (
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
    INDEX idx_data_publicacao (data_publicacao),
    INDEX idx_autor (autor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir notícias de exemplo (as que estão atualmente no site)
INSERT INTO noticias (titulo, resumo, conteudo, categoria, autor_id, data_publicacao) VALUES
('Tocha (em brasa) goleia Eirense', 'O Tocha dominou completamente o jogo e conquistou uma vitória expressiva por 5-0 contra o Eirense.', 
'O Tocha apresentou um futebol brilhante e conquistou uma vitória expressiva por 5-0 contra o Eirense. A equipa da casa dominou desde o primeiro minuto, com uma exibição coletiva de grande nível.\n\nOs golos surgiram ao longo de todo o jogo, demonstrando a superioridade técnica e tática do Tocha. O público presente no estádio assistiu a um verdadeiro espetáculo de futebol.\n\nCom este resultado, o Tocha continua a sua caminhada positiva na competição e consolida a sua posição na tabela classificativa.',
'fire', 1, '2025-11-17 14:30:00'),

('União 1919 e Nogueirense mantêm a sua invencibilidade', 'As duas equipas continuam sem conhecer a derrota na presente edição do campeonato.', 
'A União 1919 e o Nogueirense continuam a demonstrar grande consistência nesta temporada, mantendo-se invictos após mais uma jornada disputada.\n\nAmbas as equipas têm apresentado um futebol sólido, com defesas bem organizadas e ataques eficazes. A União 1919 conquistou mais uma vitória convincente, enquanto o Nogueirense garantiu um importante empate fora de casa.\n\nEsta invencibilidade coloca ambas as equipas na corrida pelos primeiros lugares da classificação, prometendo uma luta intensa até ao final da temporada.',
'shield', 1, '2025-11-17 10:15:00'),

('Academistas regressam às vitórias', 'A Académica SF conseguiu uma importante vitória após uma série de resultados menos positivos.', 
'Depois de alguns jogos sem vencer, a Académica SF voltou aos triunfos com uma exibição segura e convincente. A equipa mostrou-se mais organizada e eficaz nas suas investidas ofensivas.\n\nO treinador fez alguns ajustes táticos que surtiram efeito imediato, com a equipa a demonstrar maior coesão e confiança em campo. Os adeptos presentes no estádio celebraram com entusiasmo esta importante vitória.\n\nCom este resultado, a Académica SF procura agora encadear uma série positiva de resultados e subir na tabela classificativa.',
'trophy', 1, '2025-11-17 09:00:00'),

('Sourense soma mais uma vitória convincente', 'O Sourense continua a surpreender com exibições de grande qualidade e conquistou mais três pontos.', 
'O Sourense tem sido uma das revelações desta temporada e voltou a demonstrar a sua qualidade com mais uma vitória convincente. A equipa jogou com grande confiança e dominou o adversário durante grande parte do encontro.\n\nOs golos surgiram de jogadas bem trabalhadas, evidenciando o bom trabalho coletivo da equipa. O técnico do Sourense tem conseguido tirar o melhor rendimento do plantel disponível.\n\nCom estas exibições, o Sourense consolida-se como uma das equipas mais fortes da competição e candidata a lugares cimeiros na classificação final.',
'trophy', 1, '2025-11-16 16:45:00'),

('Análise: O meio da tabela está ao rubro', 'A luta pela manutenção e por melhores posições está extremamente competitiva nesta temporada.', 
'O meio da tabela classificativa apresenta uma competitividade extrema, com várias equipas separadas por poucos pontos. Cada jornada pode alterar significativamente as posições e as perspetivas de cada clube.\n\nAs equipas que ocupam estas posições têm alternado entre vitórias e derrotas, tornando impossível prever qual será a classificação final. Vários clubes tradicionais encontram-se nesta zona de turbulência.\n\nOs próximos jogos serão decisivos para definir quais as equipas que conseguirão ascender a posições mais tranquilas e quais terão de lutar pela permanência até às últimas jornadas.',
'emoji_events', 1, '2025-11-15 11:20:00');
