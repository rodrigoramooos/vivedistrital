
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `clubes`;

CREATE TABLE `clubes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL UNIQUE,
  `logo` varchar(255) DEFAULT NULL,
  `jogos` int(11) DEFAULT 0,
  `pontos` int(11) DEFAULT 0,
  `vitorias` int(11) DEFAULT 0,
  `empates` int(11) DEFAULT 0,
  `derrotas` int(11) DEFAULT 0,
  `golos_marcados` int(11) DEFAULT 0,
  `golos_sofridos` int(11) DEFAULT 0,
  `forma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_classificacao` (`pontos`, `golos_marcados`, `golos_sofridos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados dos clubes
INSERT INTO `clubes` (`nome`, `codigo`, `logo`, `jogos`, `pontos`, `vitorias`, `empates`, `derrotas`, `golos_marcados`, `golos_sofridos`, `forma`) VALUES
('Nogueirense', 'nogueirense', 'imgs/equipas/nogueirense.png', 5, 15, 5, 0, 0, 13, 1, 'V V V V V'),
('Tocha', 'tocha', 'imgs/equipas/tocha.png', 5, 13, 4, 1, 0, 12, 2, 'V V V V E'),
('União 1919', 'uniaocoimbra', 'imgs/equipas/uniaocoimbra.png', 4, 10, 3, 1, 0, 14, 0, 'E V V V -'),
('Sourense', 'sourense', 'imgs/equipas/sourense.png', 5, 9, 3, 0, 2, 10, 5, 'V V D D V'),
('Vigor Mocidade', 'vigor', 'imgs/equipas/vigor.png', 5, 9, 3, 0, 2, 9, 8, 'V D V V D'),
('Académica SF', 'academica', 'imgs/equipas/aac-sf.png', 5, 7, 2, 1, 2, 8, 7, 'D E V V D'),
('Esperança', 'esperanca', 'imgs/equipas/esperanca.png', 5, 7, 2, 1, 2, 7, 8, 'V D E D V'),
('Tourizense', 'tourizense', 'imgs/equipas/tourizense.png', 5, 6, 1, 3, 1, 3, 4, 'E V E D E'),
('Ançã', 'anca', 'imgs/equipas/anca.png', 5, 6, 2, 0, 3, 6, 9, 'D D V V -'),
('Carapinheirense', 'carapinheirense', 'imgs/equipas/carapinheirense.png', 5, 6, 2, 0, 3, 5, 10, 'D D V V D'),
('Eirense', 'eirense', 'imgs/equipas/eirense.png', 5, 5, 1, 2, 2, 1, 6, 'E E D V D'),
('Pedrulhense', 'pedrulhense', 'imgs/equipas/pedrulhense.png', 5, 5, 1, 2, 2, 10, 15, 'D V E D E'),
('Penelense', 'penelense', 'imgs/equipas/penelense.png', 5, 4, 1, 1, 3, 5, 8, 'V E D D D'),
('União', 'uniao', 'imgs/equipas/uniao.png', 5, 3, 0, 3, 2, 3, 6, 'D E E D E'),
('Mocidade', 'mocidade', 'imgs/equipas/mocidade.png', 5, 1, 0, 1, 4, 0, 7, 'E D D D D'),
('Poiares', 'poiares', 'imgs/equipas/poiares.png', 4, 0, 0, 0, 4, 3, 9, 'D D D D -');

SET FOREIGN_KEY_CHECKS = 1;
