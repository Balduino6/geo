<?php 
require_once './verifyadm.php';
include_once '../conexao.php';

// Dados do funcionário (foto de perfil)
$id_funcionario = $_SESSION['id_funcionario'];
$sql = "SELECT imagem_perfil FROM Funcionario WHERE id_funcionario = $id_funcionario";
$result = $conexao->query($sql);
$funcionario = $result->fetch_assoc();
$imagemPerfil = $funcionario['imagem_perfil'];

// Mensagens (opcional)
$msg = $_GET['msg'] ?? '';

// Consulta das categorias
$cat_result = $conexao->query("SELECT id_categoria, nome FROM categorias ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Serviço e Categoria</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
      width: 150px;
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
      top: 120px;
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
      top: 130px;
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
    form { 
      width: 30%; 
      margin: 0 auto;
      margin-top: 100px;
    }
      .cop {
          color: #444;
          text-align: center;
          padding: 20px;
          width: 100%;
          border-top: solid 1px #ddd;
      }
      .section-title {
          text-align: center;
          margin-bottom: 20px;
      }
      button{
        background-image: linear-gradient(to right,  #555, #444);
      }
      button:hover{
        background-image: linear-gradient(to right, #444 , #555);
        outline: none;
      }
      .btn{
        color: white;
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
    <a href="enviar_notificacao.php"><i class="bi bi-bell"></i><span class="menu-text">Notificações</span></a>
    <a href="admin_tickets.php"><i class="bi bi-ticket-perforated"></i><span class="menu-text">Tickets</span></a>
    <a href="gerar_voucher.php"><i class="bi bi-ticket-detailed"></i><span class="menu-text">Voucher</span></a>
    <a href="transacoes_admin.php"><i class="bi bi-currency-exchange"></i><span class="menu-text">Transações</span></a>
    <a href="saldo_clientes.php"><i class="bi bi-wallet2"></i><span class="menu-text">Saldo Clientes</span></a>
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
  
  <!-- Conteúdo Principal -->
  <div class="content">
        <?php if(!empty($msg)): ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <!-- Seção para cadastrar nova Categoria -->
        <div class="mb-5">
            <h2 class="section-title">Cadastrar Nova Categoria</h2>
            <form action="salvarCategoria.php" method="post">
                <div class="mb-3">
                    <label for="categoria_nome" class="form-label">Nome da Categoria</label>
                    <input type="text" class="form-control" id="categoria_nome" name="categoria_nome" required>
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn">Cadastrar Categoria</button>
            </form>
        </div>

        <!-- Seção para cadastrar Serviço -->
        <div class="mb-5">
            <h2 class="section-title">Cadastrar Serviço</h2>
            <!-- Note que o formulário agora permite o envio de arquivo (imagem) -->
            <!-- Formulário para cadastrar Serviço -->
            <form action="salvarServico.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="id_categoria" class="form-label">Categoria</label>
                    <select class="form-control" id="id_categoria" name="id_categoria" required>
                        <option value="" disabled selected>Selecione uma categoria</option>
                        <?php while($cat = $cat_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Serviço</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="preco" class="form-label">Preço (Kz)</label>
                    <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                </div>
                <div class="mb-3">
                    <label for="imagem" class="form-label">Imagem do Serviço</label>
                    <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" required>
                </div>
                <button type="submit" class="btn">Cadastrar Serviço</button>
            </form>
        </div>
    </div>
    <div class="cop">
        <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>

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
        const currentPage = window.location.pathname.split('/').pop();
        links.forEach(link => {
          const linkPage = link.getAttribute('href').split('/').pop();
          if (linkPage === currentPage) {
              link.classList.add('active');
          }
        });
    </script>
</body>
</html>
