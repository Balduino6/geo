<?php
session_start();
require '../conexao.php';

header('Content-Type: application/json');

if (!isset($_POST['pedido_id']) || !isset($_POST['estado'])) {
    echo json_encode(['success' => false, 'msg' => 'Parâmetros ausentes']);
    exit;
}

$pedido_id = intval($_POST['pedido_id']);
$estado = trim($_POST['estado']);

// Lista de estados válidos
$estados_validos = ['Pendente', 'Em andamento', 'Concluído', 'Cancelado'];
if (!in_array($estado, $estados_validos)) {
    echo json_encode(['success' => false, 'msg' => 'Estado inválido']);
    exit;
}

// Obter o id do funcionário logado
$id_funcionario = isset($_SESSION['id_funcionario']) ? intval($_SESSION['id_funcionario']) : null;

// Atualiza o estado e sempre registra o funcionário que realizou a alteração
$stmt = $conexao->prepare("UPDATE Pedidos SET estado = ?, id_funcionario = ? WHERE id = ?");
$stmt->bind_param("sii", $estado, $id_funcionario, $pedido_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    // Busca o nome do funcionário que realizou a alteração
    $stmt = $conexao->prepare("SELECT nome FROM Funcionario WHERE id_funcionario = ?");
    $stmt->bind_param("i", $id_funcionario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nome_funcionario = $row ? $row['nome'] : 'Não encontrado';
    $stmt->close();

    echo json_encode([
        'success' => true,
        'msg' => 'Estado atualizado com sucesso',
        'funcionario' => $nome_funcionario
    ]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Erro ao atualizar estado']);
}
?>
