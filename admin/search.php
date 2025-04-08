<?php 
session_start();
require '../conexao.php';

// Verifica se há uma query de pesquisa
if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo "Por favor, digite um termo para pesquisar.";
    exit;
}

$searchTerm = trim($_GET['query']);
// Debug: Verifica o termo de pesquisa
// var_dump($searchTerm);

$sql = "
    SELECT 
        p.id AS pedido_id, 
        c.nome AS cliente_nome, 
        s.nome AS servico_nome, 
        s.preco, 
        p.estado, 
        p.data_pedido, 
        p.data_entrega,
        f.nome AS funcionario_nome
    FROM Pedidos p
    INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    LEFT JOIN Funcionario f ON p.id_funcionario = f.id_funcionario
    WHERE UPPER(c.nome) LIKE UPPER(?) OR UPPER(s.nome) LIKE UPPER(?) OR UPPER(p.estado) LIKE UPPER(?)
    ORDER BY p.id DESC
";

$stmt = $conexao->prepare($sql);
$searchParam = "%$searchTerm%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultados da Pesquisa - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding-top: 120px;
        }
        .container {
            max-width: 1040px;
            margin: auto;
        }
        .titulo {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="titulo">Resultados para: "<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>"</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Preço (Kz)</th>
                        <th>Estado</th>
                        <th>Data do Pedido</th>
                        <th>Data de Entrega</th>
                        <th>Atendido por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $estado = trim($row['estado']);
                        $dataEntrega = !empty($row['data_entrega']) ? date("d/m/Y", strtotime($row['data_entrega'])) : "—";
                    ?>
                    <tr>
                        <td><?php echo $row['pedido_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['servico_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format($row['preco'], 2, ',', '.'); ?> Kz</td>
                        <td><?php echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($row['data_pedido'])); ?></td>
                        <td><?php echo $dataEntrega; ?></td>
                        <td><?php echo htmlspecialchars($row['funcionario_nome'] ?? 'Não atendido', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum resultado encontrado para a pesquisa.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conexao->close();
?>
