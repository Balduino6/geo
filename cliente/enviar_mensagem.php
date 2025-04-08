<?php
session_start();
require 'verifica.php';
require 'conexao.php';

// Verifica se o usuário está logado (cliente ou funcionário)
if (!isset($_SESSION['id_cliente']) && !isset($_SESSION['id_funcionario'])) {
    exit;
}

if (isset($_SESSION['id_cliente'])) {
    $user_id = $_SESSION['id_cliente'];
} else {
    $user_id = $_SESSION['id_funcionario'];
}

$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

// Insere a mensagem no banco
$sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iis", $user_id, $receiver_id, $message);
$stmt->execute();
$stmt->close();

// Formata a data atual
$data = date('d/m/Y H:i');

// Retorna o HTML da mensagem enviada (para o usuário logado, class 'sent')
echo '<div class="message sent"><p>' . htmlspecialchars($message) . '</p><small>' . $data . '</small></div>';
?>
