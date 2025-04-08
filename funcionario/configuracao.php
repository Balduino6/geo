<?php 
    session_start();
    require './veryFun.php';
    include_once '../conexao.php';


    // Supondo que as permissões já foram carregadas na sessão após o login:
        $menu_permissions = $_SESSION['menu_permissions'] ?? [];

        // Defina os itens disponíveis com a mesma chave padronizada.
        $available_menus = [
            "dashboard"            => '<a href="funcionario_dashboard.php"><img src="../assets/dashboard.png" alt="" class="icon" style="width: 30px;">Dashboard</a>',
            "chat_funcionario"     => '<a href="chat_funcionario.php"><img src="../assets/chat.png" alt="" class="icon" style="width: 30px;">Mensagens</a>',
            "enviar_notificacoes"  => '<a href="enviar_notificacao.php"><img src="../assets/notification.png" alt="" class="icon" style="width: 30px;">Enviar Notificações</a>',
            "tickets"              => '<a href="admin_tickets.php"><img src="../assets/tickets.png" alt="" class="icon" style="width: 30px;">Tickets</a>',
            "transacoes_admin"     => '<a href="transacoes_admin.php"><img src="../assets/transacoes.png" alt="" class="icon" style="width: 30px;">Transações</a>',
            "saldo_clientes"       => '<a href="saldo_clientes.php"><img src="../assets/saldo.png" alt="" class="icon" style="width: 30px;">Saldo dos Clientes</a>',
            "controlFunci"         => '<a href="controlFunci.php"><img src="../assets/employee.png" alt="" class="icon" style="width: 30px;">Controle de Funcionário</a>',
            "controlCliente"       => '<a href="controlCliente.php"><img src="../assets/customer.png" alt="" class="icon" style="width: 30px;">Controle de Clientes</a>',
            "exibirServicos"       => '<a href="exibirServicos.php"><img src="../assets/services.png" alt="" class="icon" style="width: 30px;">Ver Serviços</a>',
            "cadastrarServico"     => '<a href="cadastrarServico.php"><img src="../assets/registration.png" alt="" class="icon" style="width: 30px; color:#f5f5f5;">Registrar Serviços</a>',
            "registroFun"          => '<a href="registroFun.php"><img src="../assets/regFun.png" alt="" class="icon" style="width: 30px;">Registrar Funcionário</a>',
            "registroCliente"      => '<a href="registroCliente.php"><img src="../assets/regCli.png" alt="" class="icon" style="width: 30px;">Registrar  Cliente</a>',
            "relatorios"           => '<a href="relatorios.php"><img src="../assets/relatorio.png" alt="" class="icon" style="width: 30px;">Relatorios</a>',
            "gerar_voucher"        => '<a href="gerar_voucher.php"><img src="../assets/voucher.png" alt="" class="icon" style="width: 30px;">Gerar Voucher</a>',
            "pedidos"              => '<a href="pedidos.php"><img src="../assets/clienteP.png" alt="" class="icon" style="width: 30px;">Pedidos de Serviços</a>',
            "configuracao"         => '<a href="perfil.php" style="color: white;"><img src="../assets/service.png" alt="" class="icon" style="width: 30px;">Configurações</a>',
            "permissoes"           => '<a href="permissoes.php" style="color: white;">Permissões</a>',
            "movimentos_cliente"   => '<a href="movimentos_cliente.php?"><img src="../assets/debit.png" alt="" class="icon" style="width: 30px;">Movimentos de um Cliente</a>',
            "ver_notificacoes"     => '<a href="ver_notificacoes.php"><img src="../assets/notifications.png" alt="" class="icon" style="width: 30px;">Ver Notificações</a>',
        ];
    
        // Atualiza as permissões na sessão a partir do banco
        $id_funcionario = $_SESSION['id_funcionario'];
        $query = "SELECT menu_permissions FROM funcionario WHERE id_funcionario = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("i", $id_funcionario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['menu_permissions'] = json_decode($row['menu_permissions'], true);
        } else {
            $_SESSION['menu_permissions'] = [];
        }
    
        // Atualiza a variável local com as permissões recuperadas
        $menu_permissions = $_SESSION['menu_permissions'];
    
        // Após a verificação de sessão, logo após o login:
        $id_funcionario = $_SESSION['id_funcionario'];
        $query = "SELECT menu_permissions FROM funcionario WHERE id_funcionario = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("i", $id_funcionario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Armazena as permissões como array na sessão:
            $_SESSION['menu_permissions'] = json_decode($row['menu_permissions'], true);
        } else {
            $_SESSION['menu_permissions'] = [];
        }


    $_SESSION['id_funcionario'];

  // Verifique se o ID do funcionario foi fornecido
  if (isset($_SESSION['id_funcionario'])) {
    $id_funcionario = $_SESSION['id_funcionario'];

    // Use uma consulta preparada
    $stmt = $conexao->prepare("SELECT * FROM funcionario WHERE id_funcionario = ?");
    $stmt->bind_param("i", $id_funcionario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $funcionario = $resultado->fetch_assoc();
    } else {
        echo "Funcionário não encontrado.";
        exit;
    }
} else {
    echo "Usuário não especificado.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Conta - Geovane Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f1f1f1;
        margin: 0;
        padding: 0;
    }

   body {
    font-family: Arial, sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    background-color: #f1f1f1;
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        height: 120px;
        background-color: #333;
        color: white;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        z-index: 1000;
    }

    .search-box {
        flex-grow: 1; /* Faz a caixa de pesquisa crescer para ocupar o espaço central */
        display: flex;
        justify-content: center; /* Centraliza o conteúdo da caixa de pesquisa */
        margin: 0 20px; /* Margem para afastar dos outros elementos */


        /* margin-left: 20px;
        margin-right: 5px; */
        /* width: 300px; Aumenta o tamanho da caixa de pesquisa */
        /* display: flex; */
        /* align-items: center; Alinha o botão e a caixa de texto */
    }

    .search-box form {
        display: flex;
        width: 50%; /* Define uma largura fixa ou relativa para a caixa de pesquisa */
    }

    .search-box input[type="text"] {
        flex-grow: 1;
        padding: 12px;
        font-size: 16px;
        border: 2px solid #444;
        border-radius: 4px 0 0 4px;
        background-color: #575757;
        color: white;
    }

    .search-box button {
        padding: 12px 20px;
        border: 2px solid #575757;
        border-radius: 0 4px 4px 0;
        background-color: #444;
        color: white;
        cursor: pointer;
    }

    .topbar .search-box button:hover {
        background-color: #575757; /* Cor mais escura no hover */
        border-color: #444; /* Sincroniza a cor da borda no hover */
    }

    .topbar .user-info {
        display: flex;
        align-items: center; /* Centraliza verticalmente */
        margin-left: auto; /* Empurra para a direita */
        margin-right: 20px;
    }

    .topbar .user-info .username {
        margin-right: 15px; /* Espaçamento entre nome e o botão de sair */
        font-size: 18px;
        color: white;
    }

    .topbar .user-info a {

        color: whitesmoke;
        text-decoration: none;
        font-size: 25px;
        transition: color 0.3s;
        padding: 1px 3px;
        border-radius: 5%;
        margin-right: 100px;
        display: flex;

        align-items: center;

    }

    .topbar .user-info a:hover{
        color: #ddd;
        border-top: 5px solid #ddd;
        animation: 1s;
        transition: .1s;
    }

    .topbar .user-info a i{
        margin-left: 5px;
    }

    .topbar .user-info img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-right: 17px;
    }

    .topbar .logo{
        width: 200px;
        /* height: 100px; */
        /* border: solid 1px red; */
        text-align: center;
        margin-left: 20px;
        
    }
    .topbar .logo img {
        width: 185px;
        /* height: 100px; */
        border-radius: 5%;
        /* border: solid 1px red; */
    }

    .sidebar {
        width: 250px;
        background-color: #444;
        color: white;
        padding-top: 60px;
        position: fixed;
        height: 100%;
        top: 0;
        padding-left: 20px;
        padding-top: 80px;
        
    }

    .sidebar a {
        margin-top: 50px;
        text-decoration: none;
        color: white;
        display: flex;
        align-items: center;
        transition: 0.3s;
        margin-bottom: 5px;
        font-size: 20px;
        padding: 20px 4%;
    }

    .sidebar a .icon, .sidebar a .txt-icon {
        font-size: 30px;
        margin-right: 15px;
    }

    .sidebar a:hover {
        background-color: #575757;
        transition: 1s;
    }


    #servicosSubmenu{
        transition: 2s;
    }

    .content {
        margin-left: 250px;
        margin-top: 150px;
        padding: 20px;
        flex-grow: 1;
        overflow-y: auto;
        height: calc(100vh - 60px);
        /* border: 1px solid red; */
    }


    .container {
        max-width: 800px;
        margin: 100px auto;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group img {
            max-width: 100px;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        .form-group input[type="file"] {
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            color: white;
            background-color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #555;
        }

        .btn-cancel {
            background-color: #999;
        }

        .btn-cancel:hover {
            background-color: #777;
        }
    </style>
</head>

<body>

<div class="topbar">
    <div class="logo">
        <img src="../assets/logo.png" alt="Logo";>
    </div>

    <!-- Caixa de Pesquisa -->
    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Pesquisar no sistema...">
            <button type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="user-info">
        <img src="../assets/user-avatar.png" alt="User Avatar">
        <div class="username">Olá, <?php  echo  $funcionario['usuario']; ?> </div>

        <a href="./logout.php" style="color: white; margin-left: 10px;">Sair<i class="bi bi-box-arrow-right"></i></a>
    </div>
</div>

<div class="sidebar">
    <a href="funcionario.php"> <span class="icon"><i class="bi bi-house"></i></span>
    <span class="txt-link">Principal</span></a>

    <?php
    // Exibe somente os itens para os quais o funcionário tem permissão.
    foreach ($available_menus as $menu_key => $menu_html) {
        if (in_array($menu_key, $menu_permissions)) {
            echo $menu_html;
        }
    }
    ?>
    
    <!-- Sempre pode exibir links comuns como "Sair" -->
    <a href="./logout.php" style="color: white; margin-left: 10px;">
        <img src="./assets/sair.png" alt="" style="width: 20px;">
        <span class="icon"><i class="bi bi-box-arrow-right"></i></span>Sair
    </a>
</div>

<div class="content">
    <div class="container">
        <h2>Configurações de Conta</h2>
        <form action="atualizar_dados.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id_funcionario" value="<?php echo $funcionario['id_funcionario']; ?>">
            <!-- Alterar Usuário -->
            <div class="form-group">
            <label for="usuario">Usuário*</label>
            <input type="text" name="usuario" class="form-control" value="<?php echo $funcionario['usuario']; ?>" required>
            </div>

            <!-- Alterar Email -->
            <div class="form-group">
            <label for="email" class="form-label">Endereço de Email</label>
            <input type="text" class="form-control" id="email" name="email" value="<?php echo $funcionario['email']; ?>" required>
            </div>

            <!-- Alterar Palavra-Passe -->
            <div class="form-group">
                <label for="senha">Nova Palavra-Passe</label>
                <input type="password" id="senha" name="senha">
            </div>

            <div class="form-group">
                <label for="conf_senha">Confirmar Nova Palavra-Passe</label>
                <input type="password" id="conf_senha" name="conf_senha">
            </div>

            <!-- Alterar Fotografia -->
            <div class="form-group">
            <label for="imagem" class="form-label">Imagem de Perfil*</label>
            <input type="file" name="imagem" accept="image/*" class="form-control" required>
            </div>

            <!-- Botões -->
            <div class="form-actions">
                <button type="submit" class="btn">Guardar Alterações</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='funcionario.php' ">Cancelar</button>
            </div>
        </form>
    </div>
</div>


</body>
</html>
