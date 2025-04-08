<?php

require 'veryFun.php';
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
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; }

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
            margin-right: 30px;
            display: flex;
            align-items: center;
        }

        .topbar .user-info a:hover{
            color: #ddd;
            animation: 1s;
            transition: .1s;
        }

        .topbar .user-info a i{
            margin-left: 5px;
        }
        .topbar .user-info .perfil-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
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
            overflow-y: auto;
            
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
        .content {
            margin-left: 250px;
            margin-top: 120px; 
            padding: 20px; 
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
            <div>
                <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $funcionario['imagem_perfil']; ?>" alt="Perfil" class="perfil-img"></a>
                
            </div>
            <div class="username">Olá, <?php  echo  " $nomeUsuario"; ?> </div>

            <!-- <a href="perfil.php" style="margin-left: 10px;">Perfil</a> -->
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair<i class="bi bi-box-arrow-right"></i></a>
        </div>

    </div>
    <div class="sidebar">
        <a href="admin.php"> <span class="icon"><i class="bi bi-house"></i></span>
        <span class="txt-link">Principal</span></a>

        <a href="dashboard_admin.php"><img src="../assets/dashboard.png" alt="" class="icon" style="width: 30px;">Dasboard</a>

        <a href="chat_funcionario.php"><img src="../assets/chat.png" alt="" class="icon" style="width: 30px;">Mensagens</a>
        
        <a href="enviar_notificacao.php"><img src="../assets/notification.png" alt="" class="icon" style="width: 30px;">Enviar Notificacoes</a>

        <a href="ver_notificacoes.php"><img src="../assets/notifications.png" alt="" class="icon" style="width: 30px;">Ver Notificações</a>

        <a href="admin_tickets.php"><img src="../assets/tickets.png" alt="" class="icon" style="width: 30px;">Tickets</a>
    
        <a href="gerar_voucher.php"><img src="../assets/voucher.png" alt="" class="icon" style="width: 30px;">Gerar Voucher</a>

        <a href="transacoes_admin.php"><img src="../assets/transacoes.png" alt="" class="icon" style="width: 30px;">Transações</a>

        <a href="saldo_clientes.php"><img src="../assets/saldo.png" alt="" class="icon" style="width: 30px;">Saldo dos Clientes</a>

        <a href="movimentos_cliente.php?"><img src="../assets/debit.png" alt="" class="icon" style="width: 30px;">Movimentos de um Cliente</a>
    
        <a href="exibirServicos.php"><img src="../assets/services.png" alt="" class="icon" style="width: 30px;">Ver Serviços</a>

        <a href="cadastrarServico.php"><img src="../assets/registration.png" alt="" class="icon" style="width: 30px; color:#f5f5f5;">Registrar Serviços</a>

        <a href="registroFun.php"><img src="../assets/regFun.png" alt="" class="icon" style="width: 30px;">Registrar Funcionário</a>

        <a href="registroCliente.php">
        <img src="../assets/regCli.png" alt="" class="icon" style="width: 30px;">Registrar  Cliente</a>

        <a href="controlFunci.php"><img src="../assets/employee.png" alt="" class="icon" style="width: 30px;">Controle de Funcionário</a>

        <a href="controlCliente.php"><img src="../assets/customer.png" alt="" class="icon" style="width: 30px;">Controle de Clientes</a>

        <a href="adm_pedido.php"><img src="../assets/clienteP.png" alt="" class="icon" style="width: 30px;">Pedidos de Serviços</a>

        <a href="perfil.php" style="color: white;"><img src="../assets/service.png" alt="" class="icon" style="width: 30px;">Configurações</a>

        <a href="relatorios.php"><img src="../assets/relatorio.png" alt="" class="icon" style="width: 30px;">Relatorios</a>

        <a href="./logout.php"><img src="../assets/sair.png" alt="" class="icon" style="width: 30px;">Sair</a>
        
    </div>

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
</body>
</html>