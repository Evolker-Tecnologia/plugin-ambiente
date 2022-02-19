CREATE TABLE `plugin_ambiente_situacao` (
  `alguem_esta_mexendo` int(1) DEFAULT 1,
  `id` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plugin_ambiente_alteracoes` (
  `autor` VARCHAR(64),
  `alteracoes` VARCHAR(256),
  `id` int NOT NULL PRIMARY KEY AUTO_INCREMENT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `plugin_ambiente_situacao` (`alguem_esta_mexendo`, `id`) VALUES
(2, 0);
COMMIT;