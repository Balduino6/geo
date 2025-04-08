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

$partner_id = $_POST['partner_id'] ?? null;
$partner_type = $_POST['partner_type'] ?? null;
$message = trim($_POST['message'] ?? '');

if ($partner_id && $partner_type && $message != '') {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, sender_type, recipient_id, recipient_type, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $user_type, $partner_id, $partner_type, $message]);
}
?>
