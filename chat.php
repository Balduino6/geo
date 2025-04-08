<?php
session_start();
require 'conexao.php'; // arquivo com a conexão PDO

// Identifica se o usuário logado é cliente ou funcionário
if (isset($_SESSION['id_cliente'])) {
    $user_id = $_SESSION['id_cliente'];
    $user_type = 'cliente';
} elseif (isset($_SESSION['id_funcionario'])) {
    $user_id = $_SESSION['id_funcionario'];
    $user_type = 'funcionario';
} else {
    header("Location: login.php");
    exit;
}

// Obtém o ID do parceiro via GET
if (!isset($_GET['partner'])) {
    echo "ID do parceiro não informado.";
    exit;
}
$partner_id = $_GET['partner'];

// Se o usuário logado for cliente, o parceiro é funcionário; se for funcionário, o parceiro é cliente
$partner_type = ($user_type == 'cliente') ? 'funcionario' : 'cliente';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat Privado - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .chat-container {
          max-width: 800px;
          margin: auto;
          padding: 20px;
          background: #f8f8f8;
          border: 1px solid #ddd;
          border-radius: 5px;
      }
      .messages {
          height: 400px;
          overflow-y: scroll;
          border: 1px solid #ccc;
          padding: 10px;
          background: #fff;
          margin-bottom: 20px;
      }
      .message {
          padding: 5px 10px;
          margin-bottom: 10px;
          border-radius: 5px;
      }
      .message.me {
          background: #d1ffd1;
          text-align: right;
      }
      .message.partner {
          background: #d1e0ff;
          text-align: left;
      }
      .delete-btn {
          color: red;
          cursor: pointer;
          margin-left: 10px;
      }
    </style>
</head>
<body>
<div class="chat-container">
    <h3>Chat Privado</h3>
    <div class="messages" id="chatMessages">
        <!-- Mensagens serão carregadas via AJAX -->
    </div>
    <form id="chatForm">
        <div class="input-group">
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Digite sua mensagem" required>
            <input type="hidden" name="partner_id" value="<?php echo htmlspecialchars($partner_id); ?>">
            <input type="hidden" name="partner_type" value="<?php echo htmlspecialchars($partner_type); ?>">
            <button class="btn btn-primary" type="submit">Enviar</button>
        </div>
    </form>
</div>

<script>
// Função para carregar as mensagens via AJAX
function loadMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load_messages.php?partner_id=<?php echo htmlspecialchars($partner_id); ?>&partner_type=<?php echo htmlspecialchars($partner_type); ?>', true);
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('chatMessages').innerHTML = this.responseText;
        }
    };
    xhr.send();
}

// Envio de nova mensagem via AJAX
document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var message = document.getElementById('messageInput').value;
    var partner_id = "<?php echo htmlspecialchars($partner_id); ?>";
    var partner_type = "<?php echo htmlspecialchars($partner_type); ?>";
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_message.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if(this.status == 200) {
            document.getElementById('messageInput').value = '';
            loadMessages();
        }
    };
    xhr.send('message=' + encodeURIComponent(message) +
             '&partner_id=' + encodeURIComponent(partner_id) +
             '&partner_type=' + encodeURIComponent(partner_type));
});

// Função para apagar mensagem
function deleteMessage(id) {
    if(confirm("Tem certeza que deseja apagar esta mensagem?")){
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_message.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                loadMessages();
            }
        };
        xhr.send('message_id=' + encodeURIComponent(id));
    }
}

// Atualiza as mensagens a cada 3 segundos
setInterval(loadMessages, 3000);
loadMessages();
</script>
</body>
</html>
