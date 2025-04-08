<?php
require '../conexao.php';


if (isset($_POST['pedido_id']) && isset($_POST['data_entrega'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $data_entrega = $_POST['data_entrega']; // Espera o formato YYYY-MM-DD

    $stmt = $conexao->prepare("UPDATE Pedidos SET data_entrega = ? WHERE id = ?");
    $stmt->bind_param("si", $data_entrega, $pedido_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
?>
