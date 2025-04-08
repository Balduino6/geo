<?php
require_once 'verifyadm.php';
require_once '../conexao.php';

// Query para obter os vouchers, ordenados do mais recente para o mais antigo.
$sql = "SELECT voucher_code, valor, used FROM vouchers ORDER BY id DESC";
$result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Vouchers Gerados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
        }
        .container {
            margin-top: 140px;
        }
        .badge {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Vouchers Gerados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código do Voucher</th>
                <th>Valor (Kz)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['voucher_code']); ?></td>
                    <td><?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td>
                        <?php 
                        // Se 'usado' for 1, significa que o voucher foi utilizado; se 0, não foi.
                        if($row['used']) {
                            echo '<span class="badge bg-success">Usado</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark">Não usado</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button type="button" onclick="window.location.href='gerar_voucher.php' ">Voltar</button>


</div>
</body>
</html>
