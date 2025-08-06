-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/08/2025 às 02:17
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
-- Banco de dados: `ccnebd`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bolsa`
--

CREATE TABLE `bolsa` (
  `id_bolsa` int(11) NOT NULL,
  `id_sub_origem` int(11) NOT NULL,
  `id_sub_alocacao` int(11) NOT NULL,
  `id_orientador` int(11) DEFAULT NULL,
  `id_bolsista_atual` int(11) DEFAULT NULL,
  `codigo` varchar(20) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `carga_horaria` smallint(3) NOT NULL,
  `modalidade` varchar(20) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `edital_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `campus` varchar(100) NOT NULL,
  `turno` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados_estudante`
--

CREATE TABLE `dados_estudante` (
  `id_estudante` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `cod_banco` varchar(3) DEFAULT NULL,
  `agencia` varchar(10) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id_historico` int(11) NOT NULL,
  `id_estudante` int(11) NOT NULL,
  `id_bolsa` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `observacao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricao`
--

CREATE TABLE `inscricao` (
  `id_inscricao` int(11) NOT NULL,
  `id_estudante` int(11) NOT NULL,
  `id_bolsa` int(11) NOT NULL,
  `data_inscricao` datetime NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `disponibilidade` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`disponibilidade`)),
  `url_pdf_psa` text DEFAULT NULL,
  `url_pdf_fcb` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subunidade`
--

CREATE TABLE `subunidade` (
  `id_subunidade` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `codigo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `tipo` smallint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bolsa`
--
ALTER TABLE `bolsa`
  ADD PRIMARY KEY (`id_bolsa`);

--
-- Índices de tabela `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`);

--
-- Índices de tabela `dados_estudante`
--
ALTER TABLE `dados_estudante`
  ADD PRIMARY KEY (`id_estudante`),
  ADD UNIQUE KEY `matricula` (`matricula`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id_historico`);

--
-- Índices de tabela `inscricao`
--
ALTER TABLE `inscricao`
  ADD PRIMARY KEY (`id_inscricao`);

--
-- Índices de tabela `subunidade`
--
ALTER TABLE `subunidade`
  ADD PRIMARY KEY (`id_subunidade`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bolsa`
--
ALTER TABLE `bolsa`
  MODIFY `id_bolsa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dados_estudante`
--
ALTER TABLE `dados_estudante`
  MODIFY `id_estudante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inscricao`
--
ALTER TABLE `inscricao`
  MODIFY `id_inscricao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subunidade`
--
ALTER TABLE `subunidade`
  MODIFY `id_subunidade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
