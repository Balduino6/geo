<?php 
    session_start();
   require 'verifica.php';
   require '../conexao.php';
   
   if(isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente'])):

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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Geovane Services</title>
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
        .headline {
            margin-top: 100px;
        }
        .headline h2 {
            margin: 0;
            font-size: 36px;
        }
        .headline p {
            font-size: 19px;
        }
        section h2 {
            width: 100%;
            font-size: 50px;
            margin-top: 5%;
            color: #180F4A;
            font-family: 'Lobster', cursive;
            text-align: center;
        }
        .servicos {
            display: inline-block;
            margin-top: 1%;
            margin-left: 15%;
            margin-bottom: 8%;
            gap: 20px; 
            text-align: center;
        }
        .card {
            display: inline-block;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 450px;
            text-align: center;
            padding: 60px;
            margin: 15px;
            cursor: pointer;
            transition: background 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            background: #444;
            color: white;
            padding: 60px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        }
        .card img {
            height: auto;
        }
        .card-text {
            padding: 20px;
        }
        .cop {
            color: #444;
            text-align: center;
            padding: 20px;
            width: 100%;
            border-top: solid 1px #ddd;
        }
        .whats {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .whats img {
            width: 50px;
            height: 50px;
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

    <!-- Conteúdo -->
    <div class="content">
        <header class="header">
            <div class="headline">
                <h2>Bem-Vindo à GEOVANE SERVICES</h2>
                <p>Funcionalidade é a nossa função</p>
            </div>
        </header>
        <section>
            <h2 class="text-center mb-4" style="color: #180F4A; font-family: 'Lobster', cursive;">Serviços</h2>
            <div class="servicos">
                <div class="card">
                    <img src="../assets/pc.png" alt="Serviço 1">
                    <div class="card-text">
                        <h3>Desenvolvimento de Sites</h3>
                        <p>Desenvolvemos sites simples e complexos, portfólios e muito mais.</p>
                    </div>
                </div>
                <div class="card">
                    <img src="../assets/computer.png" alt="Serviço 2">
                    <div class="card-text">
                        <h3>Montagem de Redes LANS</h3>
                        <p>Montagem de redes locais cliente-servidor com cabeamento estruturado.</p>
                    </div>
                </div>
                <div class="card">
                    <img src="../assets/web.png" alt="Serviço 3">
                    <div class="card-text">
                        <h3>Manutenção de Infraestrutura de Redes</h3>
                        <p>Manutenção na infraestrutura de rede.</p>
                    </div>
                </div>
                <div class="card">
                    <img src="../assets/shooting.png" alt="Serviço 4">
                    <div class="card-text">
                        <h3>Manutenção de Computadores</h3>
                    </div>
                </div>
                <div class="card">
                    <img src="../assets/project.png" alt="Serviço 5">
                    <div class="card-text">
                        <h3>Venda de Projetos</h3>
                    </div>
                </div>
                <div class="card">
                    <img src="../assets/printer.png" alt="Serviço 6">
                    <div class="card-text">
                        <h3>Cyber Coffee</h3>
                    </div>
                </div>
            </div>
        </section>
        <div class="cop">
            <p class="copy">© Todos direitos reservados por <strong>GeovaneServices</strong></p>
        </div>
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
<?php 
else: 
    header('Location: login.php'); 
endif; 
?>
