<?php
session_start();
require 'verifica.php';
require 'conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Busca os dados do cliente
$sqlCliente = "SELECT imagem_perfil, nome, sobrenome FROM Cliente WHERE id_cliente = $id_cliente";
$result = $conexao->query($sqlCliente);
$cliente = $result->fetch_assoc();
$imagemPerfil = $cliente['imagem_perfil'];

// Processa o upload da imagem de perfil, se houver
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    $imagem      = $_FILES['imagem'];
    $nomeArquivo = time() . '_' . $imagem['name'];
    $destino     = '../upload/imagens_perfil/' . $nomeArquivo;
    if (move_uploaded_file($imagem['tmp_name'], $destino)) {
        $imagemPerfil = $nomeArquivo;
    } else {
        $erro .= "Erro ao mover o arquivo de imagem. ";
    }
}

// Recupera o saldo atual
$sqlSaldo = "SELECT saldo FROM Cliente WHERE id_cliente = $id_cliente";
$resSaldo = $conexao->query($sqlSaldo);
$rowSaldo = $resSaldo->fetch_assoc();
$saldo = $rowSaldo['saldo'];

// Total de pedidos realizados
$sqlPedidos = "SELECT COUNT(*) as total_pedidos FROM Pedidos WHERE id_cliente = $id_cliente";
$resPedidos = $conexao->query($sqlPedidos);
$rowPedidos = $resPedidos->fetch_assoc();
$totalPedidos = $rowPedidos['total_pedidos'];

// Dados para gráfico de pedidos mensais
$sqlChart = "SELECT DATE_FORMAT(data_pedido, '%Y-%m') as mes, COUNT(*) as total 
             FROM Pedidos 
             WHERE id_cliente = $id_cliente 
             GROUP BY mes 
             ORDER BY mes";
$resChart = $conexao->query($sqlChart);
$chartLabels = [];
$chartData = [];
while($row = $resChart->fetch_assoc()){
    $chartLabels[] = $row['mes'];
    $chartData[] = $row['total'];
}
$chartLabels = json_encode($chartLabels);
$chartData = json_encode($chartData);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Meu Perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Geral */
        body {
            font-family: 'Poppins', sans-serif;
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
        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg,rgb(37, 55, 83),rgb(3, 21, 48));
            border-radius: 10px;
            padding: 30px;
            color: #fff;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .hero-banner h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .hero-banner p {
            font-size: 16px;
            margin: 0;
        }
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
            background: linear-gradient(135deg,rgb(37, 55, 83),rgb(3, 21, 48));
            color: #fff;
            max-width: 350px; /* Largura máxima definida */
            width: 100%;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }
        .card i {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        /* Centraliza os cards nas colunas */
        .row > [class*='col-'] {
            display: flex;
            justify-content: center;
        }
        /* Quick Links */
        .quick-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;

            
        }
        .quick-links a {
            flex: 1 1 150px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: background 0.3s;
        }
        .quick-links a:hover {
            background: #0056b3;
        }
        .quick-links a i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            display: block;
        }
        /* Gráfico */
        .chart-card {
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            
        }
        #chartPedidos {
            max-height: 300px;
        }
        /* Rodapé */
        .cop {
            text-align: center;
            padding: 15px;
            background-color: #fff;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            margin-top: 40px;
        }
        /* Responsividade */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
            .sidebar {
                display: none;
            }
            .quick-links a {
                flex: 1 1 100%;
            }
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
            <div class="username">Olá, <?php echo $cliente['nome'] . " " . $cliente['sobrenome']; ?></div>
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
    <!-- Conteúdo do Dashboard -->
    <div class="content">
         <div class="container-fluid">
             <!-- Hero Banner -->
             <div class="hero-banner mb-4">
                 <h1>Bem-vindo, <?php echo $cliente['nome']; ?>!</h1>
                 <p>Acompanhe suas informações e gerencie suas ações rapidamente.</p>
             </div>
             <!-- Cards com Informações -->
             <div class="row">
                 <div class="col-md-6 col-lg-4">
                     <div class="card text-center">
                         <i class="bi bi-cash-stack"></i>
                         <h5>Saldo Atual</h5>
                         <p>KZ <?php echo number_format($saldo, 2, ',', '.'); ?></p>
                     </div>
                 </div>
                 <div class="col-md-6 col-lg-4">
                     <div class="card text-center">
                         <i class="bi bi-cart"></i>
                         <h5>Total de Pedidos</h5>
                         <p><?php echo $totalPedidos; ?></p>
                     </div>
                 </div>
                 <!-- Você pode adicionar mais cards se necessário -->
             </div>
             <!-- Quick Links -->
             <div class="quick-links my-4">
                 <a href="carregamento.php">
                     <i class="bi bi-wallet2"></i>
                     Carregar Conta
                 </a>
                 <a href="meus_servicos.php">
                     <i class="bi bi-tools"></i>
                     Meus Serviços
                 </a>
                 <a href="notificacoes.php">
                     <i class="bi bi-bell"></i>
                     Notificações
                 </a>
             </div>
             <!-- Gráfico de Pedidos Mensais -->
             <div class="chart-card">
                 <h5 class="mb-3">Pedidos Mensais</h5>
                 <canvas id="chartPedidos"></canvas>
             </div>
         </div>
    </div>
    <!-- Rodapé -->
    <div class="cop">
         <p>© Todos direitos reservados por GeovaneServices</p>
    </div>
    <!-- Scripts -->
    <script>
        // Inicializa o gráfico
        const ctx = document.getElementById('chartPedidos').getContext('2d');
        const chartPedidos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $chartLabels; ?>,
                datasets: [{
                    label: 'Pedidos',
                    data: <?php echo $chartData; ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
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
    </script>
</body>
</html>
