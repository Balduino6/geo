<?php 
session_start();
require 'verifica.php';
include 'conexao.php';

    // Recupera a imagem do perfil armazenada na sessão, se existir; caso contrário, usa o avatar padrão.
    $imagemPerfil = isset($_SESSION['imagemPerfil']) ? $_SESSION['imagemPerfil'] : 'default-avatar.png';
    // Define o caminho para o arquivo de imagem; se não existir, utiliza a imagem padrão.
    $caminhoImagem = is_file("../upload/imagens_perfil/" . $imagemPerfil) ? "../upload/imagens_perfil/" . $imagemPerfil : "default-avatar.png";


    // Supondo que também você armazene o nome e sobrenome do cliente na sessão:
    $nomeUser = isset($_SESSION['nomeUser']) ? $_SESSION['nomeUser'] : 'Cliente';
    $sobrenome = isset($_SESSION['sobrenome']) ? $_SESSION['sobrenome'] : '';

if(isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente'])):
    // Buscar categorias
    $categorias = $conexao->query("SELECT id_categoria, nome FROM categorias ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedir Serviço</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Para AJAX -->

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
        transition: .4s;

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

        overflow-y: auto; /* Adiciona barra de rolagem */
        transition: 2s;    /* Transição suave */

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

    #servicosSubmenu a {
        padding: 10px 20px;
        display: block;
        font-size: 18px;
        color: white;
        text-decoration: none;
    }

    #servicosSubmenu a:hover {
        background-color: #575757;
    }

    .content {
        margin-left: 250px;
        margin-top: 120px;
        padding: 20px;
    }

    .header {
        text-align: center;
        color: white;
        background-size: cover;
        background-position: center;
        padding: 50px 0;
        /* border: 1px solid red; */
        width: 102%;
    }

    .headline{
        /* border: 1px solid red; */
        margin-top: 100px;

    }

    .headline h2 {
        margin: 0;
        font-size: 36px;
        /* border: 1px solid red; */
    }

    .headline p {
        font-size: 19px;

    }
    section h2{
        width: 100%;
        font-size: 50px;
        margin-top: 5%;
        color: #180F4A;
        font-family: 'Lobster', cursive;
        text-align: center;
    }

    .servicos {
        display: inline-block;
        margin-top: 1%;
        margin-left: 15%;
        margin-bottom: 8%;
        gap: 20px; 
        text-align: center;
    }


    .card {
        display: inline-block;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        width: 450px;
        text-align: center;
        padding: 60px;
        margin: 15px;
        cursor: pointer;
    } 

    .card:hover{
        background: #444;
        color: white;
        padding: 60px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        transition: 1s;
    }

    .card img {
        height: auto;
    }

    .card-text {
        padding: 20px;
    }


    .btn-pedir-servico {
        display: inline-block;
        padding: 10px 20px;
        background-color: #444;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    .btn-pedir-servico:hover {
        background-color: #575757;
    }

    .cop {
            /* background-color: #222; */
            color: #444;
            text-align: center;
            padding: 20px;
            width: 100%;
            border-top: solid 1px #ddd;
        }

    .whats {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .whats img {
        width: 50px;
        height: 50px;
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
    <!-- Exemplo de exibição da foto de perfil -->
    <div class="user-info">
            <!-- Exibe a foto de perfil do cliente -->
            <img src="<?php echo $caminhoImagem; ?>" alt="Foto de Perfil">
            <div class="username">Olá, <?php echo htmlspecialchars($nomeUser . " " . $sobrenome); ?></div>
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair <i class="bi bi-box-arrow-right"></i></a>
        </div>

</div>
    <!-- Resto do conteúdo da página -->

    <div class="sidebar">
        <a href="cliente.php"> <span class="icon"><i class="bi bi-house"></i></span>
        <span class="txt-link">Principal</span></a>

        <!-- <a href="pedido_servico.php">Serviços</a> -->

        <a href="#">
            <span class="txt-icon"><i class="bi bi-file-earmark-person-fill"></i></span>
            <span class="txt-link">Sobre Nós</span>    
        </a>

        <a href="../chat/chat.php?partner_id=2">Acessar Chat</a>

        <a href="chat.php">Mensagens</a>
        <a href="suporte.php">Suporte</a>
        <a href="dashboard_cliente.php">Dasboard</a>
        <a href="avaliar_servico.php">Avaliação</a>
        <a href="perfil.php">Perfil</a>
        <a href="notificacoes.php">Notificações</a>

        <a href="carregamento.php">Carregamentos</a>
        <a href="meu_saldo.php">Meu Saldo</a>
        <a href="meus_servicos.php">Meus Serviços</a>

        <a href="sobre_servico.php">serviii</a>

        <a href="carregarConta.php">Carregar Conta</a>
        <a href="services.php">Serviços</a>



        <a href="#" onclick="toggleSubmenu('servicosSubmenu')">
            <span class="txt-link"><img src="../assets/services.png" alt=""class="icon" style="width: 30px; color:#f5f5f5;">Serviços</span>  
        </a>

        <div id="servicosSubmenu" class="submenu" style="display: none;">
            <a href="#"><img src="../assets/pc.png" alt="Serviço 1" class="icon" style="width: 30px; color:#f5f5f5;" >Desenvolvimento de Sites</a>

            <a href="#"><img src="../assets/computer.png" alt="Serviço 2" class="icon" style="width: 30px; color:#f5f5f5;" >Montagem de Redes LANS</a>

            <a href="#"><img src="../assets/web.png" alt="Serviço 3" class="icon" style="width: 30px; color:#f5f5f5;" >Manutenção de Infraestrutura</a>

            <a href="#"><img src="../assets/shooting.png" alt="Serviço 4" class="icon" style="width: 30px; color:#f5f5f5;" >Manutenção de Computadores</a>

            <a href="#"><img src="../assets/project.png" alt="Serviço 5" class="icon" style="width: 30px; color:#f5f5f5;" >Venda de Projetos</a>

            <a href="#"><img src="../assets/printer.png" alt="Serviço 6" class="icon" style="width: 30px; color:#f5f5f5;" >Cyber Coffee</a>
        </div>

        <a href="configuracao.php" style="color: white;"><span class="icon"><i class="bi bi-gear-wide-connected"></i></span> Configurações</a>

        <a href="./logout.php" style="color: white; margin-left: 10px;"> <span class="icon"><i class="bi bi-box-arrow-right"></i></span>Sair</a>

    </div>

<div class="content">
    <div class="container mt-5">
            <h2 class="text-center">Fazer Pedido de Serviço</h2>
            <form action="confirmar_pedido.php" method="post">
                <!-- Seleção de Categoria -->
                <div class="form-group">
                    <label for="categoria">Selecione a Categoria</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                        <option value="" disabled selected>Escolha uma categoria</option>
                        <?php while ($cat = $categorias->fetch_assoc()) { ?>
                            <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nome'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Seleção de Serviço -->
                <div class="form-group">
                    <label for="servico">Selecione o Serviço</label>
                    <select class="form-control" id="servico" name="id_servico" required>
                        <option value="" disabled selected>Escolha um serviço</option>
                    </select>
                </div>

                <!-- Exibir Preço -->
                <div class="form-group">
                    <label for="preco">Preço (KZ)</label>
                    <input type="text" class="form-control" id="preco" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Fazer Pedido</button>
                <a href="cliente.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
        

<script>
$(document).ready(function() {
    $('#categoria').change(function() {
        var id_categoria = $(this).val();
        if (id_categoria) {
            $.ajax({
                url: 'buscar_servicos.php',
                type: 'POST',
                data: {id_categoria: id_categoria},
                success: function(response) {
                    $('#servico').html(response);
                    $('#preco').val('');
                }
            });
        }
    });

    $('#servico').change(function() {
        var preco = $('option:selected', this).attr('data-preco');
        $('#preco').val(preco ? 'KZ ' + parseFloat(preco).toFixed(2).replace('.', ',') : '');
    });
});
</script>

</body>
</html>
<?php else: header('Location: login.php'); endif; ?>
