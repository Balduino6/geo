<?php
session_start();
require_once './verifyadm.php';
include_once '../conexao.php';

if (!isset($_SESSION['id_funcionario'])) {
    header("Location: login.php");
    exit;
}

$id_funcionario = $_SESSION['id_funcionario'];
// Defina o parceiro de chat: por exemplo, se o funcionário conversar com um cliente específico, você pode passar via GET, ou definir um valor padrão.
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 1; 

// Consulta as mensagens de chat entre o funcionário e o parceiro
$sql = "SELECT * FROM chat_messages 
        WHERE (sender_id = $id_funcionario AND receiver_id = $partner_id) 
           OR (sender_id = $partner_id AND receiver_id = $id_funcionario) 
        ORDER BY data ASC";
$result = $conexao->query($sql);
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat Interno - Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            max-width: 800px;
            margin: 120px auto 50px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        #chat-box {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .message {
            margin-bottom: 10px;
        }
        .message.sent {
            text-align: right;
        }
        .message.received {
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
    <!-- Topbar e Sidebar: você pode incluir a estrutura que já utiliza para funcionários ou administradores -->
    <div class="chat-container">
       <h2>Chat Interno</h2>
       <div id="chat-box">
          <?php foreach ($messages as $msg): ?>
             <div class="message <?php echo ($msg['sender_id'] == $id_funcionario) ? 'sent' : 'received'; ?>">
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo date('d/m/Y H:i', strtotime($msg['data'])); ?></small>
             </div>
          <?php endforeach; ?>
       </div>
       <form id="chat-form" class="mt-3">
           <div class="input-group">
               <input type="text" name="message" id="message" class="form-control" placeholder="Digite sua mensagem..." required>
               <button type="submit" class="btn btn-primary">Enviar</button>
           </div>
           <input type="hidden" name="receiver_id" value="<?php echo $partner_id; ?>">
       </form>
    </div>
    <script>
       $(document).ready(function(){
           $('#chat-form').submit(function(e){
               e.preventDefault();
               $.ajax({
                   url: 'enviar_mensagem.php',
                   type: 'POST',
                   data: $(this).serialize(),
                   success: function(response){
                       $('#chat-box').append(response);
                       $('#message').val('');
                       $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                   }
               });
           });
       });
    </script>
</body>
</html>
