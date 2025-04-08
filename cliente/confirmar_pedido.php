<?php
session_start();
require 'verifica.php';
require 'conexao.php';

if(!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])){
    header("Location: login.php");
    exit;
}

if(!isset($_POST['id_servico'])){
    header("Location: pedido_servico.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$id_servico = intval($_POST['id_servico']);

// Recupera os dados do serviço
$stmt = $conexao->prepare("SELECT id_servico, nome, preco, descricao FROM servicos WHERE id_servico = ?");
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    die("Serviço não encontrado.");
}
$servico = $result->fetch_assoc();
$stmt->close();

// Recupera o saldo do cliente
$stmt = $conexao->prepare("SELECT saldo FROM Cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$stmt->bind_result($saldo_atual);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Pedido</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { font-family: Arial, sans-serif; background-color: #f1f1f1; }
      .container { margin-top: 100px; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Confirmar Pedido</h2>
    <div class="card">
        <div class="card-body">
            <h4>Serviço: <?php echo htmlspecialchars($servico['nome']); ?></h4>
            <p>Descrição: <?php echo htmlspecialchars($servico['descricao']); ?></p>
            <p>Preço: KZ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></p>
            <p>Seu Saldo Atual: KZ <?php echo number_format($saldo_atual, 2, ',', '.'); ?></p>
            <?php if($saldo_atual < $servico['preco']): ?>
                <div class="alert alert-danger">Saldo insuficiente para realizar o pedido! Por favor recarregue a sua conta.</div>
                <a href="pedido_servico.php" class="btn btn-secondary">Voltar</a>
                <a href="carregamento.php" class="btn btn-secondary">Carregar Conta</a>
            <?php else: ?>
                <form action="processar_pedido.php" method="post">
                    <input type="hidden" name="id_servico" value="<?php echo $servico['id_servico']; ?>">
                    <input type="hidden" name="confirmacao" value="1">

                    <div class="form-group">
                        <label for="senha">Digite sua senha para confirmar:</label>
                        <input type="password" name="senha" id="senha" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
                    <a href="pedido_servico.php" class="btn btn-secondary">Cancelar</a>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
