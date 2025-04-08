<?php
  require_once './veryFun.php';
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


   $sql = "SELECT *FROM cliente ORDER BY id_cliente DESC";

    $resultado =$conexao->query($sql);
?> 
    
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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
    <header class="header"> 
        <div class="m-10">
            <h1>Clientes Cadastrados</h1>  
            <br> 
            <table class="table table-borderd">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Sobrenome</th>
                        <th scope="col">Identificação</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Nascimento</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Endereço</th> 
                        <th scope="col">Senha</th>
                        <th scope="col">Confirmar Senha</th>
                        <th scope="col">...</th>
                    </tr>
                </thead>
            <tbody>
                <?php 
                    while($user_data = mysqli_fetch_assoc($resultado)){
                        echo"<tr>";
                        echo"<td>".$user_data['id_cliente']."</td>";
                        echo"<td>".$user_data['nome']."</td>";
                        echo"<td>".$user_data['sobrenome']."</td>";
                        echo"<td>".$user_data['docId']."</td>";
                        echo"<td>".$user_data['telefone']."</td>";
                        echo"<td>".$user_data['data_nasc']."</td>";
                        echo"<td>".$user_data['sexo']."</td>";
                        echo"<td>".$user_data['endereco']."</td>";
                        echo"<td>".$user_data['senha']."</td>";
                        echo"<td>".$user_data['conf_senha']."</td>";
                        echo"<td>
                        <a class='btn btn-sm btn-primary' href='editarCliente.php?id_cliente=$user_data[id_cliente]'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                            <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                        </svg>
                        </a>

                        <a class='btn btn-sm btn-danger' href='deleteCliente.php?id_cliente=$user_data[id_cliente]'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                            <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                        </svg>
                        </a>
                        </td>";

                        echo"</tr>";
                    }
                ?>
            </tbody>            
        </table>
        </div>
    </header>
</div>


</body>
</html>