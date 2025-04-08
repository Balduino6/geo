<?php

    require_once './verifyadm.php';
    include_once '../conexao.php';

    // Verifica se o funcionário está logado
    if (!isset($_SESSION['id_funcionario'])) {
        exit;
    }

    $id_funcionario = $_SESSION['id_funcionario'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    // Insere a mensagem no banco de dados
    $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iis", $id_funcionario, $receiver_id, $message);
    $stmt->execute();
    $stmt->close();

    // Recupera a data atual
    $data = date('d/m/Y H:i');

    // Retorna o HTML da mensagem enviada (alinhada à direita para o funcionário)
    echo '<div class="message sent"><p>' . htmlspecialchars($message) . '</p><small>' . $data . '</small></div>';

?>

