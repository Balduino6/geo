-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 08/04/2025 às 18:55
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `geovane`
--
CREATE DATABASE IF NOT EXISTS `geovane` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `geovane`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `avaliacoes`;
CREATE TABLE IF NOT EXISTS `avaliacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_pedido` int NOT NULL,
  `avaliacao` int NOT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 20:05
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome`, `descricao`) VALUES
(1, 'Desenvolvimento Web', 'Criamos sites modernos, responsivos e personalizados para sua empresa ou portfólio. Nosso serviço inclui design, desenvolvimento front-end e back-end, e otimização para SEO.'),
(2, 'Sistemas de Gestão', 'Desenvolvemos sistemas de gestão para destintas entidades, tudo de acordo as preferências do cliente.'),
(3, 'Hardware e Software', 'Oferecemos serviços completos de manutenção, diagnóstico e reparos de computadores, garantindo alta performance e segurança dos seus equipamentos.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_messages`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 08/04/2025 às 15:49
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sobrenome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `docId` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_nasc` date DEFAULT NULL,
  `sexo` enum('masculino',' feminino') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `endereco` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `conf_senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagem_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_funcionario` int DEFAULT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_cliente`),
  KEY `id_funcionario` (`id_funcionario`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `sobrenome`, `docId`, `email`, `telefone`, `data_nasc`, `sexo`, `endereco`, `senha`, `conf_senha`, `imagem_perfil`, `id_funcionario`, `saldo`) VALUES
(1, 'Balduino', 'Geovane', '007101839LA045', 'balduino@gmail.com', '933416260', '1999-06-06', 'masculino', 'Futungo de Belas, Luanda', 'cfff95ed0120dcb7de86fc1f16d27043', 'cfff95ed0120dcb7de86fc1f16d27043', '1743467477_balduino.jpg', NULL, 119000.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_servico`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `cliente_servico`;
CREATE TABLE IF NOT EXISTS `cliente_servico` (
  `id_cliente` int NOT NULL,
  `id_servico` int NOT NULL,
  PRIMARY KEY (`id_cliente`,`id_servico`),
  KEY `id_servico` (`id_servico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--
-- Criação: 08/04/2025 às 17:53
-- Última atualização: 08/04/2025 às 17:30
--

DROP TABLE IF EXISTS `funcionario`;
CREATE TABLE IF NOT EXISTS `funcionario` (
  `id_funcionario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sobrenome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `docId` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_nasc` date DEFAULT NULL,
  `sexo` enum('masculino',' feminino') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `endereco` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `conf_senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `acesso` enum('funcionario','administrador') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_contrato` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `data_contratacao` date NOT NULL,
  `id_cliente` int DEFAULT NULL,
  `imagem_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `menu_permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_funcionario`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionario`
--

INSERT INTO `funcionario` (`id_funcionario`, `nome`, `sobrenome`, `docId`, `email`, `telefone`, `data_nasc`, `sexo`, `endereco`, `usuario`, `senha`, `conf_senha`, `acesso`, `tipo_contrato`, `data_contratacao`, `id_cliente`, `imagem_perfil`, `data_criacao`, `menu_permissions`) VALUES
(1, 'Balduino', 'Geovane', '007101837LA046', 'geovane@gmail.com', '933416260', '2000-06-06', 'masculino', 'Talatona', 'Administrador', 'cfff95ed0120dcb7de86fc1f16d27043', 'cfff95ed0120dcb7de86fc1f16d27043', 'administrador', '', '0000-00-00', NULL, '1743235191_1728385550_geo.png', '2024-09-30 17:52:14', '[\"dashboard\",\"chat_funcionario\",\"enviar_notificacoes\",\"tickets\",\"transacoes_admin\",\"saldo_clientes\",\"controlFunci\",\"controlCliente\",\"exibirServicos\",\"cadastrarServico\",\"registroFun\",\"registroCliente\",\"relatorios\",\"gerar_voucher\",\"pedidos\",\"configuracao\",\"permissoes\",\"movimentos_cliente\",\"ver_notificacoes\"]'),
(2, 'Geovane', 'Vicente', '007101836LA047', 'gvicente@gmail.com', '933413260', '1999-06-07', 'masculino', 'Belas', 'G.vicente', 'cfff95ed0120dcb7de86fc1f16d27043', 'cfff95ed0120dcb7de86fc1f16d27043', 'funcionario', '', '0000-00-00', NULL, '1742762824_bá.png', '2024-09-30 17:52:14', '[\"dashboard\",\"chat_funcionario\",\"enviar_notificacoes\",\"tickets\",\"saldo_clientes\",\"controlCliente\",\"exibirServicos\",\"registroCliente\",\"gerar_voucher\",\"pedidos\",\"configuracao\",\"movimentos_cliente\",\"ver_notificacoes\"]'),
(7, 'Silmária ', 'Bento', '007101837LA046', 'silmaria@gmail.com', '921879794', '2024-10-02', '', 'Zona verde II', 'Wanessa', 'cfff95ed0120dcb7de86fc1f16d27043', 'cfff95ed0120dcb7de86fc1f16d27043', 'funcionario', '', '0000-00-00', NULL, '1742765270_IMG-20250104-WA0017.jpg', '2025-03-23 21:27:50', '[\"dashboard\",\"chat_funcionario\",\"enviar_notificacoes\",\"tickets\",\"transacoes_admin\",\"saldo_clientes\",\"controlCliente\",\"exibirServicos\",\"registroCliente\",\"relatorios\",\"gerar_voucher\",\"pedidos\",\"configuracao\",\"movimentos_cliente\",\"ver_notificacoes\"]'),
(8, 'Ghost', 'Writer', '008985612LB589', 'ghost@outlook.com', '923565859', '1997-09-27', 'masculino', 'Rocha Pinto', 'Ghost', 'cfff95ed0120dcb7de86fc1f16d27043', 'cfff95ed0120dcb7de86fc1f16d27043', 'funcionario', 'CLT', '2025-04-08', NULL, '1744135229_Captura de ecrã_28-7-2024_11949_www.instagram.com.jpeg', '2025-04-08 18:00:29', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario_servico`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `funcionario_servico`;
CREATE TABLE IF NOT EXISTS `funcionario_servico` (
  `id_funcionario` int NOT NULL,
  `id_servico` int NOT NULL,
  PRIMARY KEY (`id_funcionario`,`id_servico`),
  KEY `id_servico` (`id_servico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `notificacoes`;
CREATE TABLE IF NOT EXISTS `notificacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `mensagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `id_cliente`, `mensagem`, `lida`, `data`) VALUES
(1, 1, 'Olá carríssimo, este é o seu voucher: BBF59AC8', 1, '2025-04-01 00:35:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `id_servico` int DEFAULT NULL,
  `data_pedido` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_entrega` datetime DEFAULT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendente',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_funcionario` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_servico` (`id_servico`),
  KEY `fk_funcionario` (`id_funcionario`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_cliente`, `id_servico`, `data_pedido`, `data_entrega`, `estado`, `status`, `id_funcionario`) VALUES
(1, 1, 1, '2025-04-01 01:36:29', NULL, 'Pendente', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 19:50
--

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE IF NOT EXISTS `servicos` (
  `id_servico` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_categoria` int DEFAULT NULL,
  PRIMARY KEY (`id_servico`),
  KEY `id_categoria` (`id_categoria`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id_servico`, `nome`, `descricao`, `preco`, `imagem`, `id_categoria`) VALUES
(1, 'Website Estático', 'Sites estáticos, responsíveis, com as especifícidades do cliente.', 85000.00, '1743233955_sites.jpg', 1),
(2, 'Website Dinâmico', 'Websites dinâmicos, bem automatizado para atender todas as nessecidades do cliente.', 101000.00, '1743234284_dinamico.png', 1),
(3, 'Sistemas de Gestão Universitária', 'Sistemas que resolvam problemas à sua universidade ou intituto de ensino superior, com diversas funcionalidades e módulos.', 197000.00, '1743234805_sigu.png', 2),
(4, 'Sistema de Gestão Empresarial', 'Tenha o controlo dos seus funcionários, colaboradores, gastos entradas e saídas, técnicas de vendas, tudo isso em um único sistema.', 215000.00, '1743234983_empresarial.png', 2),
(5, 'Manutenção de Computadores', 'Oferecemos serviços completos de manutenção, diagnóstico e reparos de computadores, garantindo alta performance e segurança dos seus equipamentos.', 5000.00, '1743623458_manutencao.jpg', 3),
(6, 'Instalação de Sistemas Operativos', 'Instalamos sistemas operativos, desde windows à MacOs', 15000.00, '1743624983_Sistema_Operativo_2.png', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `suporte`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `suporte`;
CREATE TABLE IF NOT EXISTS `suporte` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `assunto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aberto',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 08/04/2025 às 15:49
--

DROP TABLE IF EXISTS `transacoes`;
CREATE TABLE IF NOT EXISTS `transacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('carregamento','pedido') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `voucher` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `referencia_multicaixa` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pendente','confirmado','falhou') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transacoes`
--

INSERT INTO `transacoes` (`id`, `id_cliente`, `valor`, `tipo`, `voucher`, `referencia_multicaixa`, `status`, `data`) VALUES
(1, 1, 5000.00, 'carregamento', '6657EC80', 'MC492140', 'confirmado', '2025-04-01 00:43:59'),
(2, 1, 9000.00, 'carregamento', '1D8BAE6C', 'MC114118', 'confirmado', '2025-04-01 00:49:51'),
(3, 1, 90000.00, 'carregamento', 'A9709D1C', 'MC881146', 'confirmado', '2025-04-08 15:48:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vouchers`
--
-- Criação: 02/04/2025 às 14:12
-- Última atualização: 02/04/2025 às 14:12
--

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `voucher_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `used_by` int DEFAULT NULL,
  `used_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_code` (`voucher_code`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vouchers`
--

INSERT INTO `vouchers` (`id`, `voucher_code`, `valor`, `used`, `used_by`, `used_date`) VALUES
(1, 'BBF59AC8', 100000.00, 1, 1, '2025-04-01 00:35:46');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
