<?php
session_start();
require './verifyadm.php';  // Verifica se o administrador está logado
require '../conexao.php';

// Filtro por data (opcional)
$filter = "";
if (isset($_GET['start_date'], $_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $start_date = $conexao->real_escape_string($_GET['start_date']);
    $end_date   = $conexao->real_escape_string($_GET['end_date']);
    $filter = " AND p.data_pedido BETWEEN '$start_date' AND '$end_date' ";
}

// Total de pedidos
$resultTotal = $conexao->query("SELECT COUNT(*) as total FROM Pedidos WHERE 1 $filter");
$totalPedidos = $resultTotal->fetch_assoc()['total'] ?? 0;

// Primeiro pedido (mais antigo)
$resultFirst = $conexao->query("
    SELECT p.id AS pedido_id, p.data_pedido, c.nome AS cliente_nome, s.nome AS servico_nome 
    FROM Pedidos p 
    LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente 
    LEFT JOIN Servicos s ON p.id_servico = s.id_servico 
    WHERE 1 $filter
    ORDER BY p.data_pedido ASC 
    LIMIT 1
");
$firstPedido = $resultFirst->fetch_assoc();

// Último pedido (mais recente)
$resultLast = $conexao->query("
    SELECT p.id AS pedido_id, p.data_pedido, c.nome AS cliente_nome, s.nome AS servico_nome 
    FROM Pedidos p 
    LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente 
    LEFT JOIN Servicos s ON p.id_servico = s.id_servico 
    WHERE 1 $filter
    ORDER BY p.data_pedido DESC 
    LIMIT 1
");
$lastPedido = $resultLast->fetch_assoc();

// Pedido com a data de entrega mais recente (se houver)
$resultRecentDelivery = $conexao->query("
    SELECT p.id AS pedido_id, p.data_entrega, c.nome AS cliente_nome, s.nome AS servico_nome 
    FROM Pedidos p 
    LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente 
    LEFT JOIN Servicos s ON p.id_servico = s.id_servico 
    WHERE p.data_entrega IS NOT NULL $filter
    ORDER BY p.data_entrega DESC 
    LIMIT 1
");
$recentDelivery = $resultRecentDelivery->fetch_assoc();

// Distribuição de pedidos por status
$resultStatus = $conexao->query("SELECT estado, COUNT(*) as count FROM Pedidos WHERE 1 $filter GROUP BY estado");
$statusCounts = [];
while($row = $resultStatus->fetch_assoc()){
    $statusCounts[$row['estado']] = $row['count'];
}

// Média dos preços dos serviços
$resultAvg = $conexao->query("
    SELECT AVG(s.preco) as avgPreco 
    FROM Pedidos p 
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    WHERE 1 $filter
");
$avgPreco = $resultAvg->fetch_assoc()['avgPreco'] ?? 0;

// Receita total para pedidos concluídos
$resultReceita = $conexao->query("
    SELECT SUM(s.preco) AS receita_total
    FROM Pedidos p
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    WHERE p.estado = 'Concluído' $filter
");
$receitaTotal = $resultReceita->fetch_assoc()['receita_total'] ?? 0;

// Taxa de Conclusão (% de pedidos concluídos)
$resultConcluidos = $conexao->query("SELECT COUNT(*) as concluido FROM Pedidos WHERE estado = 'Concluído' $filter");
$concluidos = $resultConcluidos->fetch_assoc()['concluido'] ?? 0;
$taxaConclusao = ($totalPedidos > 0) ? round(($concluidos / $totalPedidos) * 100, 2) : 0;

// Pedidos por mês
$resultMonth = $conexao->query("SELECT DATE_FORMAT(data_pedido, '%Y-%m') AS mes, COUNT(*) AS total FROM Pedidos WHERE 1 $filter GROUP BY mes ORDER BY mes DESC");
$pedidosPorMes = [];
while ($row = $resultMonth->fetch_assoc()) {
    $pedidosPorMes[] = $row;
}

// Top 5 serviços mais solicitados
$resultTopServicos = $conexao->query("
    SELECT s.nome AS servico_nome, COUNT(*) AS total
    FROM Pedidos p
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    WHERE 1 $filter
    GROUP BY s.id_servico
    ORDER BY total DESC
    LIMIT 5
");
$topServicos = [];
while ($row = $resultTopServicos->fetch_assoc()) {
    $topServicos[] = $row;
}

// Top 5 clientes por pedidos
$resultTopClientes = $conexao->query("
    SELECT c.nome AS cliente_nome, COUNT(*) AS total
    FROM Pedidos p
    INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
    WHERE 1 $filter
    GROUP BY c.id_cliente
    ORDER BY total DESC
    LIMIT 5
");
$topClientes = [];
while ($row = $resultTopClientes->fetch_assoc()) {
    $topClientes[] = $row;
}

// Receita por mês
$resultRevenueMonth = $conexao->query("
    SELECT DATE_FORMAT(data_pedido, '%Y-%m') AS mes, SUM(s.preco) as revenue 
    FROM Pedidos p
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    WHERE 1 $filter
    GROUP BY mes
    ORDER BY mes DESC
");
$revenueByMonth = [];
while ($row = $resultRevenueMonth->fetch_assoc()) {
    $revenueByMonth[] = $row;
}

// Relatório de Funcionários
$resultFuncionarios = $conexao->query("SELECT id_funcionario, nome, sobrenome FROM funcionario");
$totalFuncionarios = $resultFuncionarios->num_rows;
$funcionarios = [];
while ($row = $resultFuncionarios->fetch_assoc()) {
    $funcionarios[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Carrega Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding-top: 120px;
        }
        .topbar {
            background-color: #333;
            color: white;
            height: 120px;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
        }
        .topbar .logo img {
            width: 185px;
            border-radius: 5px;
        }
        .sidebar {
            position: fixed;
            top: 120px;
            left: 0;
            bottom: 0;
            width: 250px;
            background-color: #444;
            padding: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .report-section {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .report-section h3 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        canvas {
            display: block;
            margin: 0 auto;
        }
        #statusChart {
            max-width: 300px;
            max-height: 300px;
        }
        #mesChart {
            max-width: 300px;
            max-height: 200px;
        }
        #revenueChart {
            max-width: 300px;
            max-height: 200px;
        }
        .print-btn {
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo">
        </div>
        <div class="user-info">
            <span>Relatórios</span>
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair</a>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin.php">Principal</a>
        <a href="exibirServicos.php">Ver Serviços</a>
        <a href="adm_pedido.php">Pedidos de Serviços</a>
        <a href="configuracao.php">Configurações</a>
    </div>
    <!-- Conteúdo Principal -->
    <div class="content">
        <!-- Filtro por Data e Botão de Impressão -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form method="GET" class="filter-form">
                <div class="d-flex align-items-center">
                    <label for="start_date" class="me-2">Data Início:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control me-2" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                    <label for="end_date" class="me-2">Data Fim:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control me-2" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
            <button class="btn btn-secondary print-btn" onclick="window.print()">Imprimir Relatório</button>
        </div>
        <!-- Relatórios -->
        <div class="report-section">
            <h3>Visão Geral</h3>
            <p><strong>Total de Pedidos:</strong> <?php echo $totalPedidos; ?></p>
            <p><strong>Média dos Preços:</strong> <?php echo number_format($avgPreco, 2, ',', '.'); ?> Kz</p>
            <p><strong>Receita Total (Concluídos):</strong> <?php echo number_format($receitaTotal, 2, ',', '.'); ?> Kz</p>
            <p><strong>Taxa de Conclusão:</strong> <?php echo $taxaConclusao; ?>%</p>
            <!-- Gráfico Donut: Distribuição de Pedidos por Status -->
            <canvas id="statusChart"></canvas>
        </div>
        <div class="report-section">
            <h3>Pedidos por Mês</h3>
            <canvas id="mesChart"></canvas>
        </div>
        <div class="report-section">
            <h3>Receita por Mês</h3>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="report-section">
            <h3>Primeiro Pedido</h3>
            <?php if ($firstPedido): ?>
                <p><strong>ID:</strong> <?php echo $firstPedido['pedido_id']; ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($firstPedido['cliente_nome'] ?? ''); ?></p>
                <p><strong>Serviço:</strong> <?php echo htmlspecialchars($firstPedido['servico_nome'] ?? ''); ?></p>
                <p><strong>Data do Pedido:</strong> <?php echo date("d/m/Y H:i", strtotime($firstPedido['data_pedido'] ?? '')); ?></p>
            <?php else: ?>
                <p>Nenhum pedido encontrado.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Último Pedido</h3>
            <?php if ($lastPedido): ?>
                <p><strong>ID:</strong> <?php echo $lastPedido['pedido_id']; ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($lastPedido['cliente_nome'] ?? ''); ?></p>
                <p><strong>Serviço:</strong> <?php echo htmlspecialchars($lastPedido['servico_nome'] ?? ''); ?></p>
                <p><strong>Data do Pedido:</strong> <?php echo date("d/m/Y H:i", strtotime($lastPedido['data_pedido'] ?? '')); ?></p>
            <?php else: ?>
                <p>Nenhum pedido encontrado.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Pedido com Entrega Mais Recente</h3>
            <?php if ($recentDelivery): ?>
                <p><strong>ID:</strong> <?php echo $recentDelivery['pedido_id']; ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($recentDelivery['cliente_nome'] ?? ''); ?></p>
                <p><strong>Serviço:</strong> <?php echo htmlspecialchars($recentDelivery['servico_nome'] ?? ''); ?></p>
                <p><strong>Data de Entrega:</strong> <?php echo date("d/m/Y", strtotime($recentDelivery['data_entrega'] ?? '')); ?></p>
            <?php else: ?>
                <p>Nenhum pedido com data de entrega definida.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Pedidos por Status</h3>
            <?php if (!empty($statusCounts)): ?>
                <ul>
                    <?php foreach ($statusCounts as $estado => $count): ?>
                        <li><strong><?php echo htmlspecialchars($estado ?? ''); ?>:</strong> <?php echo $count; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum pedido encontrado.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Pedidos por Mês</h3>
            <?php if (!empty($pedidosPorMes)): ?>
                <ul>
                    <?php foreach ($pedidosPorMes as $mesData): ?>
                        <li><strong><?php echo htmlspecialchars($mesData['mes'] ?? ''); ?>:</strong> <?php echo $mesData['total']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum dado disponível.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Top 5 Serviços Mais Solicitados</h3>
            <?php if (!empty($topServicos)): ?>
                <ol>
                    <?php foreach ($topServicos as $servicoData): ?>
                        <li><?php echo htmlspecialchars($servicoData['servico_nome'] ?? ''); ?> (<?php echo $servicoData['total']; ?> pedidos)</li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p>Nenhum dado disponível.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Top 5 Clientes por Pedidos</h3>
            <?php if (!empty($topClientes)): ?>
                <ol>
                    <?php foreach ($topClientes as $cliente): ?>
                        <li><?php echo htmlspecialchars($cliente['cliente_nome'] ?? ''); ?> (<?php echo $cliente['total']; ?> pedidos)</li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p>Nenhum dado disponível.</p>
            <?php endif; ?>
        </div>
        <div class="report-section">
            <h3>Funcionários</h3>
            <p><strong>Total de Funcionários:</strong> <?php echo $totalFuncionarios; ?></p>
            <?php if (!empty($funcionarios)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Sobrenome</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $func): ?>
                            <tr>
                                <td><?php echo $func['id_funcionario']; ?></td>
                                <td><?php echo htmlspecialchars($func['nome']); ?></td>
                                <td><?php echo htmlspecialchars($func['sobrenome']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum funcionário encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Gráfico Donut: Distribuição de Pedidos por Status
        var statusCounts = <?php echo json_encode($statusCounts); ?>;
        var labels = [];
        var dataStatus = [];
        for (var key in statusCounts) {
            labels.push(key);
            dataStatus.push(statusCounts[key]);
        }
        var ctx = document.getElementById('statusChart').getContext('2d');
        var statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataStatus,
                    backgroundColor: ['#00acc1', '#ffce56', '#4caf50', '#ff6384'],
                    hoverBackgroundColor: ['#008c9e', '#e0ac30', '#3e8e41', '#e55367']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribuição de Pedidos por Status'
                    }
                }
            }
        });

        // Gráfico de Barras: Pedidos por Mês
        var pedidosPorMes = <?php echo json_encode($pedidosPorMes); ?>;
        var meses = [];
        var totalMes = [];
        pedidosPorMes.forEach(function(item) {
            meses.push(item.mes);
            totalMes.push(item.total);
        });
        var ctx2 = document.getElementById('mesChart').getContext('2d');
        var mesChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Pedidos',
                    data: totalMes,
                    backgroundColor: '#00acc1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pedidos por Mês'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de Barras: Receita por Mês
        var revenueData = <?php echo json_encode($revenueByMonth); ?>;
        var revenueMeses = [];
        var revenueTotals = [];
        revenueData.forEach(function(item) {
            revenueMeses.push(item.mes);
            revenueTotals.push(item.revenue);
        });
        var ctx3 = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: revenueMeses,
                datasets: [{
                    label: 'Receita (Kz)',
                    data: revenueTotals,
                    backgroundColor: '#4caf50'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                plugins: {
                    title: {
                        display: true,
                        text: 'Receita por Mês'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>









