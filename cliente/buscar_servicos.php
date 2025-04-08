<?php
require 'conexao.php';

if(isset($_POST['id_categoria'])) {
    $id_categoria = intval($_POST['id_categoria']);
    
    $stmt = $conexao->prepare("SELECT id_servico, nome, preco FROM servicos WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="" disabled selected>Escolha um serviço</option>';
    while ($servico = $result->fetch_assoc()) {
        echo "<option value='{$servico['id_servico']}' data-preco='{$servico['preco']}'>{$servico['nome']}</option>";
    }

    $stmt->close();
}
?>
