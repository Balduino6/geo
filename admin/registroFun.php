<?php
    require_once 'verifyadm.php';
    require '../conexao.php';

    $id_funcionario = $_SESSION['id_funcionario'];
    // Busca os dados do funcionario, incluindo a senha atual
    $sql = "SELECT imagem_perfil FROM Funcionario WHERE id_funcionario = $id_funcionario";
    $result = $conexao->query($sql);
    $funcionario = $result->fetch_assoc();

    // Mensagens (opcional)
    $msg = $_GET['msg'] ?? '';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Funcionário</title>
    
      <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- tabela  -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
 
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
            width: 150px; /* Reduzindo de 150px para 100px */
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
      margin-top: -150px;
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

    .header{
        margin-top:250px;
        /* display: flex; */
        /* flex-flow: row wrap; */
        justify-content: center;
        align-items: center;
    }

    .m-5{
        margin-top: 50px;
    }
    .table-bg{
        background: rgba(0,0,0,0.5);
        border-radius: 20px;
    }

    .header h1{
        /* margin-left: 30%;  */
        font-size: 52px;
        text-align: center;
        align-items: center;
    }

    .header h2{
        text-align: center;
    }

    form{
        width: 800px;
        height: auto;
        margin: auto;
        
        margin-bottom: 200px;
        padding-top: 20px;
            }

    button{
        background-image: linear-gradient(to right,  #555, #444);
        color: white;
    }

    button:hover{
        background-image: linear-gradient(to right, #444 , #555);
        color: white;
        outline: none;
    }

    .btn{
        color: white;
    }

    .btn:hover{
        color: white;
    }

    .cop {
        /* background-color: #222; */
        color: #444;
        text-align: center;
        padding: 20px;
        width: 100%;
        border-top: solid 1px #ddd;
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
    <header class="header">
        <?php if(!empty($msg)): ?>
          <div class="alert alert-info text-center"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <div class="container">
        <h1 class="title">Registro de Funcionário</h1>
            <form action="cadastrarFun.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome*</label>
                    <input type="text" name="nome" class="form-control" placeholder="Digite o seu nome" required>
                </div>

                <div class="mb-3">
                    <label for="sobrenome" class="form-label">Sobrenome*</label>
                    <input type="text" name="sobrenome" class="form-control" placeholder="Digite o seu sobrenome" required>
                </div>

                <div class="mb-3">
                    <label for="docId" class="form-label">Documento de Identificação*</label>
                    <input type="text" name="docId" class="form-control" placeholder="xxxxxxxxxLAxxx" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@gmail.com" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone*</label>
                    <input type="text" name="telefone" class="form-control" placeholder="Número de telefone" required>
                </div>

                <div class="mb-3">
                    <label for="data_nasc" class="form-label">Data de Nascimento*</label>
                    <input type="date" name="data_nasc" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="sexo" class="form-label">sexo</label>
                    <select name="sexo" id="sexo" class="form-control" required >
                        <option selected disabled="" value="Selecione o seu sexo" >Selecione o seu sexo</option>
                    
                        <option value="masculino" name="sexo" class="form-control">Masculino</option>
                    
                        <option value="feminino" name="sexo" class="form-control">Feminino</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço*</label>
                    <input type="text" name="endereco" class="form-control" placeholder="Digite um endereço" required>
                </div>

                <div class="card-group"> 
                    <label for="usuario" class="form-label">Usuário*</label>
                    <input type="text" name="usuario" class="form-control" placeholder="Nome de Usuário" required> 
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha*</label>
                    <input type="password" name="senha" placeholder="No mínimo 4 caracteres" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="conf_senha" class="form-label">Confirmar senha*</label>
                    <input type="password" name="conf_senha" class="form-control" placeholder="Repita a mesma senha" required>
                </div>
                    
                <div class="mb-3">
                    <label for="acesso" class="form-label">Acesso</label>
                    <select name="acesso" id="acesso" class="form-control" required >
                        <option selected disabled="" value="Selecione o tipo de acesso" >Tipo de acesso</option>
                    
                        <option value="funcionario" name="funcionario">Funcionário</option>
                    
                        <option value="administrador" name="administrador">Administrador</option>
                    </select>
                </div>

                <div class="mb-3">
                  <label for="tipo_contrato" class="form-label">Tipo de Contrato*</label>
                  <select name="tipo_contrato" class="form-control" required>
                      <option value="">Selecione o tipo de contrato</option>
                      <option value="CLT">CLT</option>
                      <option value="PJ">PJ</option>
                      <option value="Temporário">Temporário</option>
                      <option value="Estágio">Estágio</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Data de Contratação:</label>
                  <input type="date" name="data_contratacao" class="form-control" required>
                </div>
                        
                <div class="mb-3">
                    <label for="imagem" class="form-label">Imagem de Perfil*</label>
                    <input type="file" name="imagem" accept="image/*" class="form-control" required>
                </div>

                <button type="submit" class="btn">Registrar</button>
                <button type="reset" class="btn" name="reset">Apagar</button>
                <button type="button" class="btn" onclick="window.location.href='admin.php' ">Cancelar</button>
            </form>

        </div>
    </header>
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