<?php
session_start();
require 'verifica.php';
require 'conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['id_cliente']) && !isset($_SESSION['id_funcionario'])) {
    exit;
}

if (isset($_SESSION['id_cliente'])) {
    $user_id = $_SESSION['id_cliente'];
} else {
    $user_id = $_SESSION['id_funcionario'];
}

$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 0;
if ($partner_id == 0) {
    exit;
}

$sql = "SELECT * FROM chat_messages 
        WHERE ((sender_id = $user_id AND receiver_id = $partner_id) 
        OR (sender_id = $partner_id AND receiver_id = $user_id))
        ORDER BY data ASC";
$result = $conexao->query($sql);
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
header('Content-Type: application/json');
echo json_encode($messages);
?>
