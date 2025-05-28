-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/05/2025 às 03:30
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ccne_bd`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bolsa`
--

CREATE TABLE `bolsa` (
  `id_bolsa` int(11) NOT NULL,
  `id_subunidade` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `limite_ch` int(2) NOT NULL,
  `modalidade` varchar(20) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `arquivo` text NOT NULL,
  `localizacao` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `candidato`
--

CREATE TABLE `candidato` (
  `id_canditado` int(11) NOT NULL,
  `id_bolsa` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `carga_horaria` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo` smallint(6) NOT NULL,
  `campus` varchar(50) NOT NULL,
  `turno` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estudante`
--

CREATE TABLE `estudante` (
  `id_user` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `matricula` int(11) NOT NULL,
  `telefone` int(11) NOT NULL,
  `cod_banco` char(3) NOT NULL,
  `agencia` varchar(10) NOT NULL,
  `conta` varchar(20) NOT NULL,
  `observacoes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id_historico` int(11) NOT NULL,
  `id_bolsa` int(11) NOT NULL,
  `id_candidato` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subunidade`
--

CREATE TABLE `subunidade` (
  `id_subunidade` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_user` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `tipo` smallint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bolsa`
--
ALTER TABLE `bolsa`
  ADD PRIMARY KEY (`id_bolsa`),
  ADD KEY `fk_subunidade` (`id_subunidade`);

--
-- Índices de tabela `candidato`
--
ALTER TABLE `candidato`
  ADD PRIMARY KEY (`id_canditado`),
  ADD KEY `fk_user_candidato` (`id_user`),
  ADD KEY `fk_user_bolsa` (`id_bolsa`);

--
-- Índices de tabela `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`);

--
-- Índices de tabela `estudante`
--
ALTER TABLE `estudante`
  ADD KEY `fk_user` (`id_user`),
  ADD KEY `fk_curso` (`id_curso`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `fk_bolsa_historico` (`id_bolsa`),
  ADD KEY `id_candidato` (`id_candidato`);

--
-- Índices de tabela `subunidade`
--
ALTER TABLE `subunidade`
  ADD PRIMARY KEY (`id_subunidade`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bolsa`
--
ALTER TABLE `bolsa`
  MODIFY `id_bolsa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `candidato`
--
ALTER TABLE `candidato`
  MODIFY `id_canditado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subunidade`
--
ALTER TABLE `subunidade`
  MODIFY `id_subunidade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bolsa`
--
ALTER TABLE `bolsa`
  ADD CONSTRAINT `fk_subunidade` FOREIGN KEY (`id_subunidade`) REFERENCES `subunidade` (`id_subunidade`);

--
-- Restrições para tabelas `candidato`
--
ALTER TABLE `candidato`
  ADD CONSTRAINT `fk_user_bolsa` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsa` (`id_bolsa`),
  ADD CONSTRAINT `fk_user_candidato` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`);

--
-- Restrições para tabelas `estudante`
--
ALTER TABLE `estudante`
  ADD CONSTRAINT `fk_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`);

--
-- Restrições para tabelas `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `fk_bolsa_historico` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsa` (`id_bolsa`),
  ADD CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`id_candidato`) REFERENCES `candidato` (`id_canditado`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
