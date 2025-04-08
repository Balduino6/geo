<?php
session_start();
require 'verifica.php';    // Verifica se o cliente está logado
require 'conexao.php';     // Arquivo com a conexão PDO

// Verifica se o cliente está logado
if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

// Verifica se o id do serviço foi passado
if (!isset($_GET['id_servico']) || empty($_GET['id_servico'])) {
    echo "Serviço não especificado.";
    exit;
}

$id_servico = intval($_GET['id_servico']);

// Consulta os detalhes do serviço na tabela Servicos
$stmt = $pdo->prepare("SELECT id_servico, nome, descricao, preco FROM Servicos WHERE id_servico = ?");
$stmt->execute([$id_servico]);
$servico = $stmt->fetch();

if (!$servico) {
    echo "Serviço não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($servico['nome']); ?> - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding-top: 120px;
        }
        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            height: 120px;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .sidebar {
            position: fixed;
            top: 120px;
            left: 0;
            bottom: 0;
            width: 250px;
            background-color: #444;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            text-decoration: none;
            color: white;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .service-header {
            background: url('../assets/bodyimg.jpg') center center/cover no-repeat;
            height: 300px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin-bottom: 30px;
        }
        .service-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        .service-details img {
            max-width: 100%;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo" style="width:185px; border-radius:5px;">
        </div>
        <div class="user-info">
            <img src="../assets/user-avatar.png" alt="User Avatar" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
            <div class="username">Olá, <?php echo "$nomeUser $sobrenome"; ?></div>
            <a href="logout.php" style="color:white; margin-left:10px;">Sair <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="cliente.php"><i class="bi bi-house"></i> Principal</a>
        <a href="meus_servicos.php"><i class="bi bi-gear"></i> Meus Serviços</a>
        <a href="../chat.php?partner_id=2&partner_type=funcionario"><i class="bi bi-chat-dots"></i> Iniciar Chat</a>
        <a href="configuracao.php"><i class="bi bi-gear-wide-connected"></i> Configurações</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
    <!-- Conteúdo Principal -->
    <div class="content">
        <!-- Cabeçalho do Serviço -->
        <div class="service-header">
            <h1><?php echo htmlspecialchars($servico['nome']); ?></h1>
        </div>
        <!-- Detalhes do Serviço -->
        <div class="service-details container">
            <div class="row">
                <div class="col-md-6">
                    <!-- Imagem principal (você pode definir um padrão ou buscar de outra tabela, se disponível) -->
                    <img src="../assets/default-service.jpg" alt="<?php echo htmlspecialchars($servico['nome']); ?>">
                </div>
                <div class="col-md-6">
                    <h2>Descrição</h2>
                    <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
                    <h3>Preço</h3>
                    <p><?php echo number_format($servico['preco'], 2, ',', '.'); ?> Kz</p>
                </div>
            </div>
            <!-- Se desejar, adicione uma galeria de fotos ou outras informações aqui -->
        </div>
    </div>
</body>
</html>
