<?php 
session_start();
require 'verifica.php';  // Verifica se o cliente está logado
require 'conexao.php';   // Conexão PDO

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
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

// Consulta os pedidos do cliente, unindo com os dados do serviço, categoria e funcionário
$stmt = $pdo->prepare("
    SELECT 
        p.id AS pedido_id, 
        s.nome AS servico_nome, 
        s.preco, 
        p.estado, 
        p.data_pedido, 
        p.data_entrega,
        cat.nome AS categoria_nome,
        f.nome AS funcionario_nome
    FROM Pedidos p
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    LEFT JOIN categorias cat ON s.id_categoria = cat.id_categoria
    LEFT JOIN Funcionario f ON p.id_funcionario = f.id_funcionario
    WHERE p.id_cliente = ?
    ORDER BY p.data_pedido DESC
");
$stmt->execute([$id_cliente]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Serviços - Geovane Services</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tabela -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        .table-status {
            background: white;
            border-radius: 5px;
            padding: 20px;
        }
        .cop {
            color: #444;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            width: 100%;
        }
        /* Indicador circular */
        .circular-chart {
            display: block;
            margin: 0 auto;
            max-width: 80px;
            max-height: 80px;
        }
        .circle-bg {
            fill: none;
            stroke: #eee;
            stroke-width: 3.8;
        }
        .circle {
            fill: none;
            stroke-width: 2.8;
            stroke-linecap: round;
            stroke: #00acc1;
            transition: stroke-dasharray 0.6s ease;
        }
        .percentage {
            font-size: 0.5em;
            text-anchor: middle;
            fill: #666;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
            .sidebar {
                display: none;
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
        <h2 class="text-center mb-4">Meus Pedidos de Serviço</h2>
        <div class="table-status">
            <?php if (count($pedidos) > 0): ?>
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Serviço</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Data do Pedido</th>
                            <th>Data de Entrega</th>
                            <th>Progresso</th>
                            <th>Atendido por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($pedidos as $pedido): 
                            // Calcula a porcentagem de progresso com base no estado
                            if ($pedido['estado'] === 'Pendente' || $pedido['estado'] === 'Cancelado') {
                                $percent = 0;
                            } elseif ($pedido['estado'] === 'Em andamento') {
                                $percent = 50;
                            } elseif ($pedido['estado'] === 'Concluído') {
                                $percent = 100;
                            } else {
                                $percent = 0;
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['servico_nome']); ?></td>
                            <td><?php echo !empty($pedido['categoria_nome']) ? htmlspecialchars($pedido['categoria_nome']) : "Sem Categoria"; ?></td>
                            <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($pedido['data_pedido'])); ?></td>
                            <td>
                                <?php echo !empty($pedido['data_entrega']) ? date("d/m/Y", strtotime($pedido['data_entrega'])) : "Não definida"; ?>
                            </td>
                            <td>
                                <!-- Exibe um indicador de progresso (circular) -->
                                <div class="progress-circle" data-percentage="<?php echo $percent; ?>">
                                    <svg viewBox="0 0 36 36" class="circular-chart">
                                        <path class="circle-bg"
                                            d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="circle"
                                            stroke-dasharray="<?php echo $percent; ?>, 100"
                                            stroke="#00acc1"
                                            d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <text x="18" y="20.35" class="percentage" style="fill: #666;">
                                            <?php echo $percent; ?>%
                                        </text>
                                    </svg>
                                </div>
                            </td>
                            <td>
                                <?php 
                                // Se o funcionário que atendeu o pedido estiver definido, exibe o nome; caso contrário, mostra "Não atendido"
                                echo !empty($pedido['funcionario_nome']) ? htmlspecialchars($pedido['funcionario_nome']) : "Não atendido"; 
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Você ainda não solicitou nenhum serviço.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="cop">
        <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>
    <script>
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id);
            if (submenu.style.display === "none") {
                submenu.style.display = "block";
            } else {
                submenu.style.display = "none";
            }
        }
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
        
        // Inicializa os tooltips do Bootstrap, se necessário
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
