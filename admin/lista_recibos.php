<?php
// lista_recibos.php
require '../conexao.php';

// Consulta para buscar as transações confirmadas (recibos)
$query = "SELECT t.*, c.nome AS cliente_nome 
          FROM transacoes t 
          INNER JOIN Cliente c ON t.id_cliente = c.id_cliente 
          WHERE t.status = 'confirmado' AND t.tipo = 'carregamento'
          ORDER BY t.data DESC";
$result = $conexao->query($query);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Recibos - Geovane Services</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-recibo {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lista de Recibos</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nº Recibo</th>
                    <th>ID Transação</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    // Gera número do recibo similar ao receipt.php
                    $numeroRecibo = "REC-" . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                ?>
                <tr>
                    <td><?php echo $numeroRecibo; ?></td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['cliente_nome']; ?></td>
                    <td>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo $row['data']; ?></td>
                    <td>
                        <a href="receipt.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-recibo btn-sm" target="_blank">
                            <i class="bi bi-receipt"></i> Ver Recibo
                        </a>
                        <!-- Outras ações podem ser adicionadas aqui -->
                    </td>
                </tr>
                <?php endwhile; ?>
            
            </tbody>
        </table>
    </div>
</body>
</html>
