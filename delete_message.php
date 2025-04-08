<?php
session_start();
require 'conexao.php';

if (isset($_SESSION['id_cliente'])) {
    $user_id = $_SESSION['id_cliente'];
    $user_type = 'cliente';
} elseif (isset($_SESSION['id_funcionario'])) {
    $user_id = $_SESSION['id_funcionario'];
    $user_type = 'funcionario';
} else {
    exit;
}

$message_id = $_POST['message_id'] ?? null;

if ($message_id) {
    // Verifica se a mensagem pertence ao usuário logado
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();

    if ($message && $message['sender_id'] == $user_id && $message['sender_type'] == $user_type) {
        $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE id = ?");
        $stmt->execute([$message_id]);
    }
}
?>
