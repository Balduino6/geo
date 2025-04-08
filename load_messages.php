<?php
session_start();
require 'conexao.php';

// Verifica se o usuário está logado e identifica seu tipo
if (isset($_SESSION['id_cliente'])) {
    $user_id = $_SESSION['id_cliente'];
    $user_type = 'cliente';
} elseif (isset($_SESSION['id_funcionario'])) {
    $user_id = $_SESSION['id_funcionario'];
    $user_type = 'funcionario';
} else {
    exit;
}

if (!isset($_GET['partner_id']) || !isset($_GET['partner_type'])) {
    exit;
}
$partner_id = $_GET['partner_id'];
$partner_type = $_GET['partner_type'];

// Consulta as mensagens trocadas entre os dois participantes
$stmt = $pdo->prepare("SELECT * FROM chat_messages 
    WHERE ((sender_id = ? AND sender_type = ? AND recipient_id = ? AND recipient_type = ?)
       OR (sender_id = ? AND sender_type = ? AND recipient_id = ? AND recipient_type = ?))
    ORDER BY created_at ASC");
$stmt->execute([
    $user_id, $user_type, $partner_id, $partner_type,
    $partner_id, $partner_type, $user_id, $user_type
]);
$messages = $stmt->fetchAll();

foreach ($messages as $msg) {
    // Se a mensagem foi enviada pelo usuário logado, classe "me"; caso contrário, "partner"
    $class = ($msg['sender_id'] == $user_id && $msg['sender_type'] == $user_type) ? 'me' : 'partner';
    echo '<div class="message ' . $class . '">';
    echo '<strong>' . (($class == 'me') ? 'Você' : 'Parceiro') . ':</strong> ';
    echo htmlspecialchars($msg['message']);
    // Permite apagar apenas as mensagens enviadas pelo usuário logado
    if ($msg['sender_id'] == $user_id && $msg['sender_type'] == $user_type) {
        echo '<span class="delete-btn" onclick="deleteMessage(' . $msg['id'] . ')">Apagar</span>';
    }
    echo '<br><small>' . $msg['created_at'] . '</small>';
    echo '</div>';
}
?>
