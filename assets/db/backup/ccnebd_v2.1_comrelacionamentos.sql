-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/08/2025 às 02:42
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
  ADD PRIMARY KEY (`id_bolsa`),
  ADD KEY `fk_bolsa_subunidade_origem` (`id_sub_origem`),
  ADD KEY `fk_bolsa_subunidade_alocacao` (`id_sub_alocacao`),
  ADD KEY `fk_bolsa_orientador` (`id_orientador`),
  ADD KEY `fk_bolsa_bolsista_atual` (`id_bolsista_atual`);

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
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `fk_dados_estudante_curso` (`id_curso`),
  ADD KEY `fk_dados_estudante_usuario` (`id_usuario`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `fk_historico_bolsa` (`id_bolsa`),
  ADD KEY `fk_historico_bolsista` (`id_estudante`);

--
-- Índices de tabela `inscricao`
--
ALTER TABLE `inscricao`
  ADD PRIMARY KEY (`id_inscricao`),
  ADD KEY `fk_inscricao_bolsa` (`id_bolsa`),
  ADD KEY `fk_inscricao_estudante` (`id_estudante`);

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

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bolsa`
--
ALTER TABLE `bolsa`
  ADD CONSTRAINT `fk_bolsa_bolsista_atual` FOREIGN KEY (`id_bolsista_atual`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bolsa_orientador` FOREIGN KEY (`id_orientador`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bolsa_subunidade_alocacao` FOREIGN KEY (`id_sub_alocacao`) REFERENCES `subunidade` (`id_subunidade`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bolsa_subunidade_origem` FOREIGN KEY (`id_sub_origem`) REFERENCES `subunidade` (`id_subunidade`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `dados_estudante`
--
ALTER TABLE `dados_estudante`
  ADD CONSTRAINT `fk_dados_estudante_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dados_estudante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `fk_historico_bolsa` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsa` (`id_bolsa`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historico_bolsista` FOREIGN KEY (`id_estudante`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `inscricao`
--
ALTER TABLE `inscricao`
  ADD CONSTRAINT `fk_inscricao_bolsa` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsa` (`id_bolsa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscricao_estudante` FOREIGN KEY (`id_estudante`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
