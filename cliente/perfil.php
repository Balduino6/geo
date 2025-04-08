<?php
session_start();
require 'verifica.php';
require 'conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Busca os dados do cliente, incluindo a senha atual
$sql = "SELECT nome, sobrenome, email, telefone, endereco, imagem_perfil, senha FROM Cliente WHERE id_cliente = $id_cliente";
$result = $conexao->query($sql);
$cliente = $result->fetch_assoc();

$mensagemSucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza dados pessoais
    $nome       = $_POST['nome'];
    $sobrenome  = $_POST['sobrenome'];
    $email      = $_POST['email'];
    $telefone   = $_POST['telefone'];
    $endereco   = $_POST['endereco'];

    // Processa a imagem de perfil, se houver upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem    = $_FILES['imagem'];
        $nomeArquivo = time() . '_' . $imagem['name'];
        $destino   = '../upload/imagens_perfil/' . $nomeArquivo;
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            $imagemPerfil = $nomeArquivo;
        } else {
            $erro .= "Erro ao mover o arquivo de imagem. ";
            $imagemPerfil = $cliente['imagem_perfil'];
        }
    } else {
        $imagemPerfil = $cliente['imagem_perfil'];
    }
    
    // Atualiza os dados pessoais
    $update = "UPDATE Cliente SET nome = '$nome', sobrenome = '$sobrenome', email = '$email', telefone = '$telefone', endereco = '$endereco', imagem_perfil = '$imagemPerfil' WHERE id_cliente = $id_cliente";
    if (!$conexao->query($update)) {
        $erro .= "Erro ao atualizar os dados: " . $conexao->error . " ";
    }
    
    // Verifica se o cliente deseja alterar a senha
    if (!empty($_POST['senha_atual']) || !empty($_POST['nova_senha']) || !empty($_POST['conf_nova_senha'])) {
        $senha_atual     = md5($_POST['senha_atual']);
        $nova_senha      = md5($_POST['nova_senha']);
        $conf_nova_senha = md5($_POST['conf_nova_senha']);
        
        // Valida se a senha atual está correta
        if ($senha_atual !== $cliente['senha']) {
            $erro .= "Senha atual incorreta. ";
        } elseif ($nova_senha !== $conf_nova_senha) {
            $erro .= "Nova senha e confirmação não coincidem. ";
        } else {
            // Atualiza a senha
            $updateSenha = "UPDATE Cliente SET senha = '$nova_senha' WHERE id_cliente = $id_cliente";
            if (!$conexao->query($updateSenha)) {
                $erro .= "Erro ao atualizar a senha: " . $conexao->error . " ";
            } else {
                $mensagemSucesso .= "Senha atualizada com sucesso. ";
            }
        }
    }
    
    if (empty($erro)) {
        $mensagemSucesso .= "Dados atualizados com sucesso.";
        // Atualiza os dados do cliente para exibir os novos valores
        $sql = "SELECT nome, sobrenome, email, telefone, endereco, imagem_perfil, senha FROM Cliente WHERE id_cliente = $id_cliente";
        $result = $conexao->query($sql);
        $cliente = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Geovane Services</title>
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
        .perfil-card {
            max-width: 600px;
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
        <!-- Caixa de Pesquisa -->
        <div class="search-box">
            <form action="search.php" method="GET" class="d-flex">
                <input type="text" name="query" placeholder="Pesquisar..." class="form-control me-2">
                <button type="submit" class="btn btn-outline-light"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <!-- Dados do Usuário -->
        <div class="user-info">
            <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $cliente['imagem_perfil']; ?>" alt="Perfil"></a>
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
                    <?php if (!empty($cliente['imagem_perfil'])): ?>
                        <img src="../upload/imagens_perfil/<?php echo $cliente['imagem_perfil']; ?>" alt="Perfil" class="perfil-img"><br><br>
                    <?php endif; ?>
                    <label for="imagem" class="form-label">Alterar Imagem de Perfil</label>
                    <input type="file" name="imagem" id="imagem" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome:</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sobrenome:</label>
                    <input type="text" name="sobrenome" class="form-control" value="<?php echo htmlspecialchars($cliente['sobrenome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone:</label>
                    <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço:</label>
                    <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($cliente['endereco']); ?>">
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
                <a href="cliente.php" class="btn btn-secondary">Cancelar</a>
            </form>
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