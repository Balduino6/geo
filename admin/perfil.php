<?php

require 'verifyadm.php';
require '../conexao.php';

if (!isset($_SESSION['id_funcionario']) || empty($_SESSION['id_funcionario'])) {
    header("Location: login.php");
    exit;
}

$id_funcionario = $_SESSION['id_funcionario'];

// Busca os dados do funcionario, incluindo a senha atual
$sql = "SELECT nome, sobrenome, email, telefone, endereco, imagem_perfil, usuario, senha FROM Funcionario WHERE id_funcionario = $id_funcionario";
$result = $conexao->query($sql);
$funcionario = $result->fetch_assoc();

$mensagemSucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza dados pessoais
    $nome       = $_POST['nome'];
    $sobrenome  = $_POST['sobrenome'];
    $email      = $_POST['email'];
    $telefone   = $_POST['telefone'];
    $endereco   = $_POST['endereco'];
    $usuario    = $_POST['usuario'];

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
    
    // Atualiza os dados pessoais
    $update = "UPDATE Funcionario SET nome = '$nome', sobrenome = '$sobrenome', email = '$email', telefone = '$telefone', endereco = '$endereco', usuario = '$usuario', imagem_perfil = '$imagemPerfil' WHERE id_funcionario = $id_funcionario";
    if (!$conexao->query($update)) {
        $erro .= "Erro ao atualizar os dados: " . $conexao->error . " ";
    }
    
    // Verifica se o cliente deseja alterar a senha
    if (!empty($_POST['senha_atual']) || !empty($_POST['nova_senha']) || !empty($_POST['conf_nova_senha'])) {
        $senha_atual     = md5($_POST['senha_atual']);
        $nova_senha      = md5($_POST['nova_senha']);
        $conf_nova_senha = md5($_POST['conf_nova_senha']);
        
        // Valida se a senha atual está correta
        if ($senha_atual !== $funcionario['senha']) {
            $erro .= "Senha atual incorreta. ";
        } elseif ($nova_senha !== $conf_nova_senha) {
            $erro .= "Nova senha e confirmação não coincidem. ";
        } else {
            // Atualiza a senha
            $updateSenha = "UPDATE Funcionario SET senha = '$nova_senha' WHERE id_funcionario = $id_funcionario";
            if (!$conexao->query($updateSenha)) {
                $erro .= "Erro ao atualizar a senha: " . $conexao->error . " ";
            } else {
                $mensagemSucesso .= "Senha atualizada com sucesso. ";
            }
        }
    }
    
    if (empty($erro)) {
        $mensagemSucesso .= "Dados atualizados com sucesso.";
        // Atualiza os dados do funcionario para exibir os novos valores
        $sql = "SELECT nome, sobrenome, email, telefone, endereco, usuario, imagem_perfil, senha FROM Funcionario WHERE id_funcionario = $id_funcionario";
        $result = $conexao->query($sql);
        $funcionario = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .perfil-card {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .perfil-card h2 { margin-bottom: 20px; }

        .perfil-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
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
        <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $funcionario['imagem_perfil']; ?>" alt="Perfil"></a>
    
      <span><?php echo $nomeUsuario; ?></span>
      <a href="logout.php" class="ms-3 text-white"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </div>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
  <!-- <a href="admin.php" title="Principal"><i class="bi bi-house"></i><span class="menu-text">Principal</span></a> -->

    <a href="dashboard_admin.php" title="Dashboard"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
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
        <div class="perfil-card">
            <h2>Meu Perfil</h2>
            <?php if (!empty($mensagemSucesso)): ?>
                <div class="alert alert-success"><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            <form action="perfil.php" method="post" enctype="multipart/form-data">
                <div class="mb-3 text-center">
                    <?php if (!empty($funcionario['imagem_perfil'])): ?>
                        <img src="../upload/imagens_perfil/<?php echo $funcionario['imagem_perfil']; ?>" alt="Perfil" class="perfil-img"><br><br>
                    <?php endif; ?>
                    <label for="imagem" class="form-label">Alterar Imagem de Perfil</label>
                    <input type="file" name="imagem" id="imagem" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome:</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($funcionario['nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sobrenome:</label>
                    <input type="text" name="sobrenome" class="form-control" value="<?php echo htmlspecialchars($funcionario['sobrenome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($funcionario['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone:</label>
                    <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($funcionario['telefone']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço:</label>
                    <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($funcionario['endereco']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Usuario:</label>
                    <input type="text" name="usuario" class="form-control" value="<?php echo htmlspecialchars($funcionario['usuario']); ?>">
                </div>
                <hr>
                <h4>Alterar Senha</h4>
                <div class="mb-3">
                    <label class="form-label">Senha Atual:</label>
                    <input type="password" name="senha_atual" class="form-control" placeholder="Digite sua senha atual">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nova Senha:</label>
                    <input type="password" name="nova_senha" class="form-control" placeholder="Digite sua nova senha">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar Nova Senha:</label>
                    <input type="password" name="conf_nova_senha" class="form-control" placeholder="Confirme sua nova senha">
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                <a href="admin.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
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