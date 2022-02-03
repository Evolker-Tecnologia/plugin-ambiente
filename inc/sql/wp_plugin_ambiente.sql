--
-- Estrutura da tabela `plug_ambiente`
--

CREATE TABLE `plug_ambiente` (
  `alguem_esta_mexendo` int(1) DEFAULT 1,
  `id` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `plug_ambiente`
--

INSERT INTO `plug_ambiente` (`alguem_esta_mexendo`, `id`) VALUES
(2, 0);
COMMIT;