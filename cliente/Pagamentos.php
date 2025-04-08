<?php
require_once '../../config/db.php';
require_once '../../models/Pagamento.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login.php');
    exit();
}

$pagamento = new Pagamento($conn);
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $valor = $_POST['valor'];
    $metodo = $_POST['metodo'];
    
    if ($pagamento->registrarPagamento($cliente_id, $valor, $metodo)) {
        $mensagem = "Pagamento registrado com sucesso!";
    } else {
        $mensagem = "Erro ao registrar pagamento.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Pagamentos</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main>
        <h1>Registrar Pagamento</h1>
        <?php if ($mensagem): ?>
            <p><?php echo $mensagem; ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="cliente_id">Cliente ID:</label>
            <input type="text" name="cliente_id" required>
            
            <label for="valor">Valor:</label>
            <input type="number" name="valor" step="0.01" required>
            
            <label for="metodo">Método de Pagamento:</label>
            <select name="metodo">
                <option value="dinheiro">Dinheiro</option>
                <option value="transferencia">Transferência</option>
                <option value="cartao">Cartão</option>
            </select>
            
            <button type="submit">Registrar</button>
        </form>
    </main>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
