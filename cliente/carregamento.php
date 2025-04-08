<?php
session_start();
require 'verifica.php';
require '../conexao.php';

if (!isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carregar Conta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container-custom {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-option {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .info-box {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="container-custom">
        <h2 class="text-center text-primary">Carregar Conta</h2>
        <p class="text-center">Escolha um método de pagamento para adicionar saldo à sua conta:</p>
        
        <div class="text-center">
            <!-- Botão para pagamento via Multicaixa -->
            <a href="carregarConta_tradicional.php" class="btn btn-primary btn-option">
                <i class="fas fa-credit-card"></i> Pagar via Multicaixa
            </a>
            
            <!-- Botão para pagamento via voucher -->
            <a href="resgatar_voucher.php" class="btn btn-success btn-option">
                <i class="fas fa-ticket-alt"></i> Pagar com Voucher
            </a>

            <!-- Botão para voltar ao painel -->
            <a href="cliente.php" class="btn btn-outline-secondary btn-option">
            <i class="bi bi-arrow-left"></i> Voltar ao Painel
            </a>
        </div>

        <div class="info-box mt-4">
            <h5>Como funciona:</h5>
            <ul>
                <li><strong>Pagar via Multicaixa:</strong> Gere uma referência de pagamento e realize o pagamento em uma agência autorizada. Após a confirmação, seu saldo será atualizado.</li>
                <li><strong>Pagar com Voucher:</strong> Se você possui um código de voucher, utilize-o para carregar sua conta instantaneamente.</li>
            </ul>
        </div>
    </div>
</div>

<!-- FontAwesome para ícones -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
