<?php
// receipt.php
require '../conexao.php';

if (!isset($_GET['id'])) {
    die("ID da transação não fornecido.");
}

$id = intval($_GET['id']);

// Busca os dados da transação e informações do cliente
$query = "SELECT t.*, c.nome AS cliente_nome, c.email, c.telefone 
          FROM transacoes t 
          INNER JOIN Cliente c ON t.id_cliente = c.id_cliente 
          WHERE t.id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$transacao = $result->fetch_assoc();
if (!$transacao) {
    die("Transação não encontrada.");
}
$stmt->close();

// Gerar número de recibo (exemplo: REC-00001)
$numeroRecibo = "REC-" . str_pad($transacao['id'], 5, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Transação - <?php echo $numeroRecibo; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .receipt-header, .receipt-footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 28px;
            color: #007bff;
        }
        .receipt-header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .receipt-details {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .receipt-details th, .receipt-details td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .receipt-details th {
            background: #f7f7f7;
        }
        .print-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="../assets/logo.png" alt="Logo da Empresa" style="max-width:150px;">
            <h1>Recibo de Transação</h1>
            <p><strong>Número do Recibo:</strong> <?php echo $numeroRecibo; ?></p>
            <p>Geovane Services</p>
            <p>Futungo de Belas, Luanda, Angola</p>
            <p>Telefone: (+244) 933416260 | Email: geovaneservices@gmail.com</p>
        </div>

        <h2>Dados do Cliente</h2>
        <table class="receipt-details">
            <tr>
                <th>Nome</th>
                <td><?php echo $transacao['cliente_nome']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $transacao['email'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Telefone</th>
                <td><?php echo $transacao['telefone'] ?? 'N/A'; ?></td>
            </tr>
        </table>

        <h2>Detalhes da Transação</h2>
        <table class="receipt-details">
            <tr>
                <th>ID da Transação</th>
                <td><?php echo $transacao['id']; ?></td>
            </tr>
            <tr>
                <th>Data</th>
                <td><?php echo $transacao['data']; ?></td>
            </tr>
            <tr>
                <th>Tipo</th>
                <td><?php echo ucfirst($transacao['tipo']); ?></td>
            </tr>
            <tr>
                <th>Valor</th>
                <td>Kz <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo ucfirst($transacao['status']); ?></td>
            </tr>
            <tr>
                <th>Voucher</th>
                <td><?php echo $transacao['voucher']; ?></td>
            </tr>
            <tr>
                <th>Referência</th>
                <td><?php echo $transacao['referencia_multicaixa']; ?></td>
            </tr>
        </table>

        <div class="receipt-footer">
            <p>Obrigado pela sua preferência!</p>
            <button class="print-btn" onclick="window.print()">Imprimir Recibo</button>
        </div>
    </div>
</body>
</html>
