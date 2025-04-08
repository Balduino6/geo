<?php 
session_start();
require 'verifica.php';
require '../conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header('Location: login.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
    // Busca os dados do cliente, incluindo nome, sobrenome e imagem de perfil
    $sqlCliente = "SELECT imagem_perfil, nome, sobrenome FROM Cliente WHERE id_cliente = $id_cliente";
    $result = $conexao->query($sqlCliente);
    $cliente = $result->fetch_assoc();
    $imagemPerfil = $cliente['imagem_perfil'];
    $nomeUser = $cliente['nome'];
    $sobrenome = $cliente['sobrenome'];

    // Processa o upload da imagem de perfil, se houver upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem    = $_FILES['imagem'];
        $nomeArquivo = time() . '_' . $imagem['name'];
        $destino   = '../upload/imagens_perfil/' . $nomeArquivo;
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            $imagemPerfil = $nomeArquivo;
        } else {
            $erro .= "Erro ao mover o arquivo de imagem. ";
        }
    }

// Consulta os serviços do banco de dados
$sqlServicos = "SELECT id_servico, nome, preco, descricao, imagem FROM Servicos";
$resultServicos = $conexao->query($sqlServicos);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Serviços Detalhados - Geovane Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
 /* Geral */
 body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #343a40;
            color: #fff;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1100;
            height: 120px;
        }
        .topbar .logo img {
            width: 200px;
            height: 100px;
            border-radius: 5%;
        }
        .topbar .user-info {
            display: flex;
            align-items: center;
        }
        .topbar .user-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #212529;
            position: fixed;
            top: 120px; /* abaixo da topbar */
            bottom: 0;
            left: 0;
            padding: 20px 0;
            transition: width 0.3s;
            overflow-x: hidden;
            z-index: 1050;
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: #adb5bd;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #495057;
            color: #fff;
        }
        .sidebar a i {
            font-size: 1.5rem;
            margin-right: 15px;
            transition: margin 0.3s;
        }
        .sidebar.collapsed a .menu-text {
            display: none;
        }
        .sidebar.collapsed a i {
            margin-right: 0;
            text-align: center;
            width: 100%;
        }
        /* Conteúdo */
        .content {
            margin-top: 50px;
            margin-left: 250px;
            padding: 100px 30px 30px;
            transition: margin-left 0.3s;
        }
        .sidebar.collapsed ~ .content {
            margin-left: 80px;
        }
        /* Botão de Toggle */
        .toggle-btn {
            position: fixed;
            top: 130px; /* abaixo da topbar */
            left: 260px;
            z-index: 1200;
            background: #212529;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            transition: left 0.3s;
        }
        .sidebar.collapsed ~ .toggle-btn {
            left: 90px;
        }
        /* Header da Página */
        .header {
            background: url(../assets/bodyimg.jpg) no-repeat center center;
            background-size: cover;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .header h2 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 20px;
        }
        /* Cards de Serviços Detalhados - Grid Responsivo */
        .servicos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1040px;
            margin: 0 auto 30px;
        }
        .servico-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .servico-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .servico-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .servico-detalhes {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .servico-detalhes h3 {
            margin-top: 0;
            font-size: 20px;
        }
        .servico-detalhes p {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .btn-pedir-servico {
            font-size: 14px;
            padding: 8px 16px;
            align-self: flex-start;
        }
        /* Rodapé */
        .cop {
            color: #444;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            width: 100%;
        }
    </style>
</head>
<body>
 <!-- Topbar -->
 <div class="topbar">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo">
        </div>
        <!-- Caixa de Pesquisa -->
        <div class="search-box">
            <form action="search.php" method="GET" class="d-flex">
                <input type="text" name="query" placeholder="Pesquisar..." class="form-control me-2">
                <button type="submit" class="btn btn-outline-light"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <!-- Dados do Usuário -->
        <div class="user-info">
            <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $imagemPerfil; ?>" alt="Perfil"></a>
            <div class="username">Olá, <?php echo $nomeUser . " " . $sobrenome; ?></div>
            <a href="logout.php" class="ms-3 text-white"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
         <a href="cliente.php"><i class="bi bi-house"></i> <span class="menu-text">Principal</span></a>
         <a href="dashboard_cliente.php"><i class="bi bi-speedometer2"></i> <span class="menu-text">Dashboard</span></a>
         <a href="suporte.php"><i class="bi bi-life-preserver"></i> <span class="menu-text">Suporte</span></a>
         <a href="notificacoes.php"><i class="bi bi-bell"></i> <span class="menu-text">Notificações</span></a>
         <a href="carregamento.php"><i class="bi bi-wallet2"></i> <span class="menu-text">Carregar Conta</span></a>
         <a href="meu_saldo.php"><i class="bi bi-cash-stack"></i> <span class="menu-text">Meu Saldo</span></a>
         <a href="meus_servicos.php"><i class="bi bi-tools"></i> <span class="menu-text">Meus Serviços</span></a>
         <a href="services.php"><i class="bi bi-cart-plus"></i> <span class="menu-text">Serviços</span></a>
         <a href="perfil.php"><i class="bi bi-gear-wide-connected"></i> <span class="menu-text">Configurações</span></a>
         <a href="#"><i class="bi bi-info-circle"></i> <span class="menu-text">Sobre Nós</span></a>
         <a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span class="menu-text">Sair</span></a>
    </div>
    <!-- Botão de Toggle da Sidebar -->
    <button class="toggle-btn" id="toggle-btn"><i class="bi bi-chevron-left"></i></button>

    <!-- Conteúdo Principal -->
    <div class="content">
        <!-- Header -->
        <header class="header">
            <div>
                <h2>Conheça Nossos Serviços</h2>
                <p>Detalhamos cada serviço para melhor atender suas necessidades.</p>
            </div>
        </header>
        <!-- Seção de Serviços Detalhados -->
        <section class="servicos">
            <?php if($resultServicos && $resultServicos->num_rows > 0): ?>
                <?php while($servico = $resultServicos->fetch_assoc()): ?>
                    <div class="servico-card">
                        <img src="../upload/servicos/<?php echo $servico['imagem']; ?>" alt="<?php echo htmlspecialchars($servico['nome']); ?>">
                        <div class="servico-detalhes">
                            <h3><?php echo htmlspecialchars($servico['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
                            <p><strong>Preço:</strong> <?php echo number_format($servico['preco'], 2, ',', '.'); ?> Kz</p>
                            <a href="pedido_servico.php?servico=<?php echo $servico['id_servico']; ?>" class="btn btn-primary btn-pedir-servico">Pedir Serviço</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Nenhum serviço cadastrado.</p>
            <?php endif; ?>
        </section>
    </div>
    <!-- Rodapé -->
    <div class="cop">
        <p>© Todos os direitos reservados por <strong>GeovaneServices</strong></p>
    </div>

   <script>
        // Toggle da Sidebar
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            this.innerHTML = sidebar.classList.contains('collapsed') 
                ? '<i class="bi bi-chevron-right"></i>' 
                : '<i class="bi bi-chevron-left"></i>';
        });
        
        // Marca o link ativo conforme a URL atual
        const links = document.querySelectorAll('.sidebar a');
        const currentUrl = window.location.href;
        links.forEach(link => {
            if (currentUrl.indexOf(link.href) !== -1) {
                link.classList.add('active');
            }
        });
        
        // Atualiza o menu ativo ao clicar (opcional)
        links.forEach(link => {
            link.addEventListener('click', function() {
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Inicializa os tooltips do Bootstrap, se necessário
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
