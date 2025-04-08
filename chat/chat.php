<?php
session_start();
require '../cliente/verifica.php';
require_once '../cliente/usuarioClass.php';
require '../conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
// Para esse exemplo, vamos supor que o chat é com um funcionário cujo ID é passado via GET
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 2;

// Opcional: Você pode armazenar o nome do arquivo da imagem na sessão durante o login
$imagemPerfil = isset($_SESSION['imagemPerfil']) ? $_SESSION['imagemPerfil'] : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Estilos básicos do chat */
        .chat-container {
            max-width: 800px;
            margin: 150px auto 50px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        #chat-box {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }
        .message {
            margin-bottom: 10px;
        }
        .sent {
            text-align: right;
        }
        .received {
            text-align: left;
        }
        .message p {
            display: inline-block;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        .sent p { background-color: #dcf8c6; }
        .received p { background-color: #f1f0f0; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Exemplo de Topbar (adicione conforme sua estrutura) -->
    <div class="topbar" style="position: fixed; top:0; width:100%; background:#333; color:white; padding:10px 20px;">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo" style="width:150px; border-radius:5px;">
        </div>
        <div class="user-info" style="margin-left:auto; display:flex; align-items:center;">
            <img src="../upload/imagens_perfil/<?php echo $imagemPerfil; ?>" alt="Foto de Perfil" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
            <div class="username">Olá, <?php echo $_SESSION['nomeUser'] . " " . $_SESSION['sobrenome']; ?></div>
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
    <!-- Exemplo de Sidebar (pode ser incluída com include se já existir) -->
    <div class="sidebar" style="position:fixed; top:120px; left:0; width:250px; background:#444; padding:20px; height:calc(100% - 120px);">
        <a href="cliente.php" style="color:white; display:block; margin-bottom:10px;"><i class="bi bi-house"></i> Principal</a>
        <a href="pedido_servico.php" style="color:white; display:block; margin-bottom:10px;">Pedir Serviço</a>
        <a href="meu_saldo.php" style="color:white; display:block; margin-bottom:10px;">Meu Saldo</a>
        <a href="perfil.php" style="color:white; display:block; margin-bottom:10px;">Perfil</a>
        <a href="notificacoes.php" style="color:white; display:block; margin-bottom:10px;">Notificações</a>
    </div>
    <!-- Conteúdo do Chat -->
    <div class="chat-container">
        <h2>Chat com Funcionário</h2>
        <div id="chat-box">
            <!-- As mensagens serão carregadas via AJAX -->
        </div>
        <form id="chat-form">
            <div class="input-group">
                <input type="text" name="message" id="message" class="form-control" placeholder="Digite sua mensagem..." required>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
            <input type="hidden" name="receiver_id" value="<?php echo $partner_id; ?>">
        </form>
    </div>
    <script>
        // Função para carregar as mensagens do chat
        function carregarMensagens() {
            $.ajax({
                url: 'ler_mensagens.php',
                data: { partner_id: <?php echo $partner_id; ?> },
                dataType: 'json',
                success: function(data) {
                    $('#chat-box').html('');
                    data.forEach(function(msg) {
                        // Se a mensagem foi enviada pelo usuário logado, class 'sent', senão 'received'
                        let className = (msg.sender_id == <?php echo $id_cliente; ?>) ? 'sent' : 'received';
                        let messageHtml = '<div class=\"message ' + className + '\"><p>' + msg.message + '</p><small>' + msg.data + '</small></div>';
                        $('#chat-box').append(messageHtml);
                    });
                    // Scroll para a última mensagem
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            });
        }

        // Atualiza o chat a cada 3 segundos
        setInterval(carregarMensagens, 3000);
        // Carrega as mensagens ao iniciar a página
        $(document).ready(function(){
            carregarMensagens();
        });

        // Envio de mensagem via AJAX
        $('#chat-form').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: 'enviar_mensagem.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Opcional: você pode atualizar o chat imediatamente aqui ou confiar no polling para atualizar
                    carregarMensagens();
                    $('#message').val('');
                }
            });
        });
    </script>
</body>
</html>
