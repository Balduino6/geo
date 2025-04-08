<?php
    require_once './veryFun.php';
    include_once '../conexao.php';

    // Verifique se o ID do cliente foi fornecido
    if(isset($_GET['id_cliente'])) {
        $id_cliente = $_GET['id_cliente'];
        $sql = "SELECT * FROM cliente WHERE id_cliente = $id_cliente";
        $resultado = $conexao->query($sql);

        if($resultado->num_rows > 0) {
            $cliente = $resultado->fetch_assoc();
        } else {
            echo "Cliente não encontrado.";
            exit;
        }
    } else {
        echo "ID do cliente não especificado.";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- icons  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- tabela  -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
  
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
        /* margin-top: 150px; */
        padding: 20px;
        flex-grow: 1;
        overflow-y: auto;
        height: calc(100vh - 60px);
        /* border: 1px solid red; */
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
    }

    button:hover{
        background-image: linear-gradient(to right, #444 , #555);

        outline: none;
    }

    .btn{
        color: white;
    }

    .btn:hover{
        color: white;
    }   
</style>
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
        <a href="logout.php" style="color: white; margin-left: 10px;">Sair<i class="bi bi-box-arrow-right"></i></a>
    </div>

</div>

<div class="sidebar">
    <a href="funcionario.php"> <span class="icon"><i class="bi bi-house"></i></span>
    <span class="txt-link">Principal</span></a>
    <a href="funcionario_pedido.php">Pedidos de Serviços</a>
    <a href="./controlCliente.php">Controle de Clientes</a>
    <a href="funcionarioServicos.php">Ver Serviços</a>
    <a href="registroCliente.php">
         <span class="icon"><i class="bi bi-r-circle-fill"></i></span>
        <span class="txt-link">Registrar Cliente</span>
    </a>
    <a href="configuracao.php" style="color: white;"><span class="icon"><i class="bi bi-gear-wide-connected"></i></span> Configurações</a>
    <a href="./logout.php" style="color: white; margin-left: 10px;"><img src="./assets/sair.png" alt="" style="width: 20px;"> <span class="icon"><i class="bi bi-box-arrow-right"></i></span>Sair</a>
</div>

<div class="content">
    <header class="header">
        <div class="container">
            <h2>Editar Cliente</h2>
            <form action="actualizarCliente.php" method="post">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $cliente['nome']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="sobrenome" class="form-label">Sobrenome</label>
                    <input type="text" class="form-control" id="sobrenome" name="sobrenome" value="<?php echo $cliente['sobrenome']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="docId" class="form-label">Documento de Identificação</label>
                    <input type="text" class="form-control" id="docId" name="docId" value="<?php echo $cliente['docId']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $cliente['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $cliente['telefone']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="data_nasc" class="form-label">Data de Nascimento</label>
                    <input type="text" class="form-control" id="data_nasc" name="data_nasc" value="<?php echo $cliente['data_nasc']; ?>" required>
                </div>

                <div class="mb-3">
                <label for="sexo" class="form-label">Sexo</label>  
                <select name="sexo" id="sexo" required>
                    <option selected disabled value="">Selecione o seu sexo</option>
                    <option value="masculino" <?php if($cliente['sexo'] == 'masculino') echo 'selected'; ?>>Masculino</option>
                    <option value="feminino" <?php if($cliente['sexo'] == 'feminino') echo 'selected'; ?>>Feminino</option>
                </select>

                </div>

                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $cliente['endereco']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="varchar" class="form-control" id="senha" name="senha" value="<?php echo $cliente['senha']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="conf_senha" class="form-label">Confirmar Senha</label>
                    <input type="varchar" class="form-control" id="conf_senha" name="conf_senha" value="<?php echo $cliente['conf_senha']; ?>" required>
                </div>

                <button type="submit" class="btn">Salvar Alterações</button>
                <button type="reset" class="btn" name="reset">Apagar</button>
                <button type="button" class="btn btn-cancel" onclick="window.location.href='controlCliente.php' ">Cancelar</button>
            </form>
        </div>
    </header>
</div>
    
</body>
</html>
