<?php
    require './veryFun.php';
    require '../conexao.php';

    // Query para obter pedidos e informações relacionadas
    $query = "
        SELECT p.id AS pedido_id, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome, s.nome AS servico_nome, s.preco
        FROM Pedidos p
        INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
        INNER JOIN Servicos s ON p.id_servico = s.id_servico
        ORDER BY p.id DESC
    ";
    $result = $conexao->query($query);

    // Verificação de erros na query
    if (!$result) {
        die("Erro na query: " . $conexao->error);
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos dos Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- icons  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- tabela  -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
</head>
<body>

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
        <div class="container mt-5">
            <h2 class="text-center">Pedidos dos Clientes</h2>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID do Pedido</th>
                        <th>Nome do Cliente</th>
                        <th>Sobrenome do Cliente</th>
                        <th>Nome do Serviço</th>
                        <th>Preço</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['pedido_id']; ?></td>
                            <td><?php echo $row['cliente_nome']; ?></td>
                            <td><?php echo $row['cliente_sobrenome']; ?></td>
                            <td><?php echo $row['servico_nome']; ?></td>
                            <td>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </header>
</div>

</body>
</html>
