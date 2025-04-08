<?php 
    require './verifyadm.php';
    include_once '../conexao.php';

    // Verifique se o ID do funcionario foi fornecido
    if(isset($_GET['id_funcionario'])) {
        $id_funcionario = $_GET['id_funcionario'];
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = $id_funcionario";
        $resultado = $conexao->query($sql);

        if($resultado->num_rows > 0) {
            $funcionario = $resultado->fetch_assoc();
        } else {
            echo "Funcionário não encontrado.";
            exit;
        }
    } else {
        echo "ID do Funcionario não especificado.";
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
        overflow-y: auto;
        transition: .2s;
        
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
        <div class="username">Olá, <?php  echo  " $nomeUsuario"; ?> </div>

        <a href="./logout.php" style="color: white; margin-left: 10px;">Sair<i class="bi bi-box-arrow-right"></i></a>
    </div>
</div>

<div class="sidebar">
    <a href="admin.php"> <span class="icon"><i class="bi bi-house"></i></span>
    <span class="txt-link">Principal</span></a>
   
    <a href="exibirServicos.php"><img src="../assets/services.png" alt="" class="icon" style="width: 30px;">Ver Serviços</a>

    <a href="cadastrarServico.php"><img src="../assets/registration.png" alt="" class="icon" style="width: 30px; color:#f5f5f5;">Registrar Serviços</a>

    <a href="registroFun.php"><img src="../assets/regFun.png" alt="" class="icon" style="width: 30px;">Registrar Funcionário</a>

    <a href="registroCliente.php">
    <img src="../assets/regCli.png" alt="" class="icon" style="width: 30px;">Registrar  Cliente</a>

    <a href="controlCliente.php"><img src="../assets/customer.png" alt="" class="icon" style="width: 30px;"> Controle de Clientes</a>

    <a href="controlFunci.php"><img src="../assets/employee.png" alt="" class="icon" style="width: 30px;">Controle de Funcionário</a>

    <a href="adm_pedido.php"><img src="../assets/clienteP.png" alt="" class="icon" style="width: 30px;">Pedidos de Serviços</a>

    <a href="configuracao.php" style="color: white;"><img src="../assets/service.png" alt="" class="icon" style="width: 30px;">Configurações</a>

    <a href="./logout.php" style="color: white; margin-left: 10px;"><span class="icon"><i class="bi bi-box-arrow-right"></i></span>Sair</a>
        
</div>

<div class="content">
    <div class="container">
        <h2>Configurações de Conta</h2>
        <form action="actualizarFuncionario.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_funcionario" value="<?php echo $funcionario['id_funcionario']; ?>">

            <!-- Alterar Nome -->
            <div class="form-group">
            <label for="usuario">Usuário*</label>
            <input type="text" name="usuario" class="form-control" required>
            </div>

            <!-- Alterar Email -->
            <!-- <div class="form-group">
                <label for="email">Endereço de Email</label>
                <input type="email" id="email" name="email" value="usuario@example.com">
            </div> -->

            <!-- Alterar Fotografia -->
            <div class="form-group">
                <label for="foto">Alterar Fotografia</label><br>
                <img src="./assets/user-avatar.png" alt="Fotografia Atual">
                <input type="file" id="foto" name="foto">
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

            <!-- Botões -->
            <div class="form-actions">
                <button type="submit" class="btn">Guardar Alterações</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='admin.php' ">Cancelar</button>
            </div>
        </form>
    </div>
</div>


</body>
</html>
