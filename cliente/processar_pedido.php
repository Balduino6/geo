<?php
    session_start();
    require 'conexao.php';

    if (!isset($_SESSION['id_cliente'])) {
        header("Location: login.php");
        exit;
    }

    if (!isset($_POST['id_servico']) || !isset($_POST['senha'])) {
        header("Location: pedido_servico.php");
        exit;
    }

    $id_cliente = $_SESSION['id_cliente'];
    $id_servico = intval($_POST['id_servico']);
    $senha_informada = $_POST['senha'];

    // Recupera a senha do cliente no banco
    $stmt = $conexao->prepare("SELECT senha, saldo FROM Cliente WHERE id_cliente = ?");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $stmt->bind_result($senha_hash, $saldo_atual);
    $stmt->fetch();
    $stmt->close();

    // Verifica a senha usando MD5 (NÃO RECOMENDADO PARA SEGURANÇA)
    if (md5($senha_informada) !== $senha_hash) {
        echo "<script>alert('Senha incorreta! Pedido não realizado.'); window.location.href='pedido_servico.php';</script>";
        exit;
    }

    // Recupera os dados do serviço
    $stmt = $conexao->prepare("SELECT preco FROM servicos WHERE id_servico = ?");
    $stmt->bind_param("i", $id_servico);
    $stmt->execute();
    $stmt->bind_result($preco_servico);
    $stmt->fetch();
    $stmt->close();

    // Verifica saldo suficiente
    if ($saldo_atual < $preco_servico) {
        echo "<script>alert('Saldo insuficiente!'); window.location.href='pedido_servico.php';</script>";
        exit;
    }

    // Deduz saldo e registra o pedido
    $novo_saldo = $saldo_atual - $preco_servico;

    $conexao->begin_transaction();

    try {
        // Atualiza o saldo do cliente
        $stmt = $conexao->prepare("UPDATE Cliente SET saldo = ? WHERE id_cliente = ?");
        $stmt->bind_param("di", $novo_saldo, $id_cliente);
        $stmt->execute();
        
        // Registra o pedido
        $stmt = $conexao->prepare("INSERT INTO Pedidos (id_cliente, id_servico, estado, data_pedido) VALUES (?, ?, 'Pendente', NOW())");
        $stmt->bind_param("ii", $id_cliente, $id_servico);
        $stmt->execute();
        
        $conexao->commit();
        
        echo "<script>alert('Pedido realizado com sucesso!'); window.location.href='pedidos_sucesso.php';</script>";
    } catch (Exception $e) {
        $conexao->rollback();
        echo "<script>alert('Erro ao processar pedido. Tente novamente.'); window.location.href='pedido_servico.php';</script>";
    }

?>
