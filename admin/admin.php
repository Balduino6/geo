<?php 
   require 'verifyadm.php';
   require '../conexao.php';
   
// filtrar admin
// $result = $conexao->query("SELECT id_funcionario, nome FROM Funcionario WHERE acesso <> 'administrador'");


    $id_funcionario = $_SESSION['id_funcionario'];
    // Busca os dados do funcionario, incluindo a senha atual
    $sql = "SELECT imagem_perfil FROM Funcionario WHERE id_funcionario = $id_funcionario";
    $result = $conexao->query($sql);
    $funcionario = $result->fetch_assoc();

    // Processa a imagem de perfil, se houver upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem    = $_FILES['imagem'];
        $nomeArquivo = time() . '_' . $imagem['name'];
        $destino   = '../upload/imagens_perfil/' . $nomeArquivo;
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            $imagemPerfil = $nomeArquivo;
        } else {
            $erro .= "Erro ao mover o arquivo de imagem. ";
            $imagemPerfil = $funcionario['imagem_perfil'];
        }
    } else {
        $imagemPerfil = $funcionario['imagem_perfil'];
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
    <!-- tabela  -->
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
            width: 200px; /* Reduzindo de 150px para 100px */
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
        text-align: center;
        color: white;
        background-size: cover;
        background-position: center;
        padding: 50px 0;
        /* border: 1px solid red; */
        width: 102%;
    }

    .headline{
        /* border: 1px solid red; */
        margin-top: 100px;

    }

    .headline h2 {
        margin: 0;
        font-size: 36px;
        /* border: 1px solid red; */
    }

    .headline p {
        font-size: 19px;

    }

    section h2{
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
    } 

    .card:hover{
        background: #444;
        color: white;
        padding: 60px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        transition: 1s;

    }

    .card img {
        /* width: 100%; */
        height: auto;
    }

    .card-text {
        padding: 20px;
    }

    .cop {
        /* background-color: #222; */
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
    <div class="search-box">
      <form action="search.php" method="GET" class="d-flex">
        <input type="text" name="query" placeholder="Pesquisar..." class="form-control me-2">
        <button type="submit" class="btn btn-outline-light"><i class="bi bi-search"></i></button>
      </form>
    </div>
    <div class="user-info">
    <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $imagemPerfil; ?>" alt="Perfil"></a>
      
      <span><?php echo $nomeUsuario; ?></span>
      <a href="logout.php" class="ms-3 text-white"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </div>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
  <a href="admin.php" title="Principal"><i class="bi bi-house"></i><span class="menu-text">Principal</span></a>

    <a href="dashboard_admin.php"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
    <!-- <a href="chat_funcionario.php"><i class="bi bi-chat-left-text"></i><span class="menu-text">Mensagens</span></a> -->
    <a href="enviar_notificacao.php"><i class="bi bi-bell"></i><span class="menu-text">Notificações</span></a>
    <a href="admin_tickets.php"><i class="bi bi-ticket-perforated"></i><span class="menu-text">Tickets</span></a>
    <a href="gerar_voucher.php"><i class="bi bi-ticket-detailed"></i><span class="menu-text">Voucher</span></a>
    <a href="transacoes_admin.php"><i class="bi bi-currency-exchange"></i><span class="menu-text">Transações</span></a>
    <a href="saldo_clientes.php"><i class="bi bi-wallet2"></i><span class="menu-text">Saldo Clientes</span></a>
    <a href="movimentos_cliente.php"><i class="bi bi-bar-chart"></i><span class="menu-text">Movimentos</span></a>
    <a href="exibirServicos.php"><i class="bi bi-tools"></i><span class="menu-text">Serviços</span></a>
    <a href="cadastrarServico.php"><i class="bi bi-plus-circle"></i><span class="menu-text">Registrar Serviços</span></a>
    <a href="registroFun.php"><i class="bi bi-person-plus"></i><span class="menu-text">Registrar Funcionário</span></a>
    <a href="registroCliente.php"><i class="bi bi-person-plus"></i><span class="menu-text">Registrar Cliente</span></a>
    <a href="controlFunci.php"><i class="bi bi-people"></i><span class="menu-text">Controle Funcionário</span></a>
    <a href="controlCliente.php"><i class="bi bi-people"></i><span class="menu-text">Controle Clientes</span></a>
    <a href="pedidos.php"><i class="bi bi-receipt"></i><span class="menu-text">Pedidos</span></a>
    <a href="perfil.php"><i class="bi bi-gear"></i><span class="menu-text">Configurações</span></a>
    <a href="permissoes.php" title="Permissões"><i class="bi bi-shield-lock"></i><span class="menu-text">Permissões</span></a>

    <a href="relatorios.php"><i class="bi bi-file-earmark-text"></i><span class="menu-text">Relatórios</span></a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span class="menu-text">Sair</span></a>
  </div>
  <!-- Botão de Toggle -->
  <button class="toggle-btn" id="toggleBtn"><i class="bi bi-chevron-left"></i></button>

    <div class="content">
        <header class="header" style="background-image: url(../assets/bodyimg.jpg); height: 300px;">
            <div class="headline">
                <h2>Bem-Vindo à GEOVANE SERVICES</h2>
                <p>Funcionalidade é a nossa função</p>
            </div>
        </header>

        <section>
            <h2>Serviços</h2>
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
                            <p>Montagem de redes locais virtuais cliente servidor com cabeamento estruturado.</p>
                        </div>
                    </div>

                    <div class="card">
                        <img src="../assets/web.png" alt="Serviço 3">
                        <div class="card-text">
                            <h3>Manutenção de Infraestrutura de Redes</h3>
                            <p>Manutenção na infraestrutura de rede.</p>
                        </div>
                    </div>
                    <br>

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
            <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
        </div>

        <!-- <a class="whats" href="https://wa.link/cwu58y" target="_blank">
            <img src="../assets/whats.png" alt="WhatsApp">
        </a> -->
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
        // Toggle da sidebar
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        this.innerHTML = sidebar.classList.contains('collapsed') 
            ? '<i class="bi bi-chevron-right"></i>' 
            : '<i class="bi bi-chevron-left"></i>';
        });
        
        const links = document.querySelectorAll('.sidebar a');
        const currentPage = window.location.pathname.split('/').pop(); // Pega o nome do arquivo atual

        links.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

    </script>
</body>
</html>