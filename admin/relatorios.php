<?php

    require './verifyadm.php';  // Verifica se o administrador está logado
    require '../conexao.php';

    $id_funcionario = $_SESSION['id_funcionario'];
    // Busca os dados do funcionario, incluindo a senha atual
    $sql = "SELECT imagem_perfil FROM Funcionario WHERE id_funcionario = $id_funcionario";
    $result = $conexao->query($sql);
    $funcionario = $result->fetch_assoc();

    // Processa a imagem de perfil, se houver upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem    = $_FILES['imagem'];
        $nomeArquivo = time() . '_' . $imagem['name'];
        $destino   = '../upload/imagens_perfil/' . $nomeArquivo;
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            $imagemPerfil = $nomeArquivo;
        } else {
            $erro .= "Erro ao mover o arquivo de imagem. ";
            $imagemPerfil = $funcionario['imagem_perfil'];
        }
    } else {
        $imagemPerfil = $funcionario['imagem_perfil'];
    }


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
    while ($row = $resultStatus->fetch_assoc()) {
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

    // Valor Médio dos Pedidos Concluídos
    $valorMedio = ($concluidos > 0) ? $receitaTotal / $concluidos : 0;

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

    // Top 5 clientes por pedidos (resumido)
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

    // Detalhes dos Clientes (Top 10 detalhado)
    $resultClientDetails = $conexao->query("
        SELECT c.id_cliente, c.nome, c.sobrenome, COUNT(p.id) as total_pedidos, AVG(s.preco) as media_preco
        FROM cliente c
        LEFT JOIN Pedidos p ON c.id_cliente = p.id_cliente
        LEFT JOIN Servicos s ON p.id_servico = s.id_servico
        GROUP BY c.id_cliente
        ORDER BY total_pedidos DESC
        LIMIT 10
    ");
    $clientDetails = [];
    while ($row = $resultClientDetails->fetch_assoc()){
        $clientDetails[] = $row;
    }

    // // Executar a consulta corrigida
    // $resultDetails = $conexao->query("
    //     SELECT 
    //         f.id_funcionario, 
    //         f.nome AS funcionario_nome, 
    //         c.id_cliente, 
    //         c.nome AS cliente_nome,
    //         s.id_servico,
    //         s.nome AS servico_nome
    //     FROM Funcionario f
    //     LEFT JOIN Pedidos p ON f.id_funcionario = p.id_funcionario
    //     LEFT JOIN Cliente c ON p.id_cliente = c.id_cliente
    //     LEFT JOIN Servicos s ON p.id_servico = s.id_servico
    //     ORDER BY f.id_funcionario, c.nome, s.nome
    // ");

    $resultDetails = $conexao->query("
        SELECT 
            f.id_funcionario,
            f.nome AS funcionario_nome,
            f.sobrenome AS funcionario_sobrenome,
            c.id_cliente,
            c.nome AS cliente_nome,
            COUNT(p.id) AS total_pedidos
        FROM Pedidos p
        INNER JOIN Funcionario f ON p.id_funcionario = f.id_funcionario
        INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
        GROUP BY f.id_funcionario, c.id_cliente
        ORDER BY f.id_funcionario, total_pedidos DESC
    ");

    $relatorio = [];
    while ($row = $resultDetails->fetch_assoc()) {
        $funcionario_id = $row['id_funcionario'];
        if (!isset($relatorio[$funcionario_id])) {
            $relatorio[$funcionario_id] = [
                'nome'      => trim(($row['funcionario_nome'] ?? '') . ' ' . ($row['sobrenome'] ?? '')),
                'clientes'  => []
                
            ];
        }
        // Adiciona os clientes com o total de pedidos
        $relatorio[$funcionario_id]['clientes'][] = [
            'cliente_nome' => trim($row['cliente_nome']),
            'total' => $row['total_pedidos']

        ];
    }


// $funcionarios = [];
// while ($row = $resultDetails->fetch_assoc()) {
//     $funcionario_id = $row['id_funcionario'];
    
//     // Apenas crie o registro do funcionário se ele já tiver algum pedido ou se você quiser listar todos mesmo sem pedidos.
//     if (!isset($funcionarios[$funcionario_id])) {
//         $funcionarios[$funcionario_id] = [
//             'nome'      => trim(($row['funcionario_nome'] ?? '') . ' ' . ($row['sobrenome'] ?? '')),
//             'clientes'  => [],
//             'servicos'  => []
//         ];
//     }

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

    // Relatório de Funcionários (Básico)
    $resultFuncionarios = $conexao->query("SELECT id_funcionario, nome, sobrenome FROM funcionario");
    $totalFuncionarios = $resultFuncionarios->num_rows;
    $funcionarios = [];
    while ($row = $resultFuncionarios->fetch_assoc()) {
        $funcionarios[] = $row;
    }

    // Análise Detalhada: Tempo médio de entrega (em horas e dias) para pedidos com data de entrega
    $resultAvgDelivery = $conexao->query("
        SELECT AVG(TIMESTAMPDIFF(HOUR, data_pedido, data_entrega)) as avg_hours,
            AVG(TIMESTAMPDIFF(DAY, data_pedido, data_entrega)) as avg_days
        FROM Pedidos
        WHERE data_entrega IS NOT NULL $filter
    ");
    if ($resultAvgDelivery && $resultAvgDelivery->num_rows > 0) {
        $avgDeliveryData = $resultAvgDelivery->fetch_assoc();
        $avgDeliveryHours = $avgDeliveryData['avg_hours'] ?? 0;
        $avgDeliveryDays  = $avgDeliveryData['avg_days'] ?? 0;
    } else {
        $avgDeliveryHours = 0;
        $avgDeliveryDays = 0;
    }

    // Relatório Desempenho: Pedidos por Dia da Semana
    $resultWeekday = $conexao->query("SELECT DAYOFWEEK(data_pedido) AS weekday, COUNT(*) AS total FROM Pedidos WHERE 1 $filter GROUP BY weekday ORDER BY weekday");
    $pedidosWeekday = [];
    while ($row = $resultWeekday->fetch_assoc()){
        $pedidosWeekday[] = $row;
    }
    $weekDays = [1 => "Domingo", 2 => "Segunda", 3 => "Terça", 4 => "Quarta", 5 => "Quinta", 6 => "Sexta", 7 => "Sábado"];

    // Relatório Desempenho: Pedidos Pendentes há Mais de 7 Dias (incluindo cliente e serviço)
    $resultPending7 = $conexao->query("
        SELECT p.id AS pedido_id, p.data_pedido, p.estado, c.nome AS cliente_nome, s.nome AS servico_nome
        FROM Pedidos p
        INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
        INNER JOIN Servicos s ON p.id_servico = s.id_servico
        WHERE p.estado = 'Pendente' 
        AND p.data_pedido <= DATE_SUB(NOW(), INTERVAL 7 DAY) $filter
        ORDER BY p.data_pedido ASC
    ");
    $pending7 = [];
    while ($row = $resultPending7->fetch_assoc()){
        $pending7[] = $row;
    }

    // Relatório Desempenho: Comparação Mensal
    $currentMonth = date("Y-m");
    $resultCurrent = $conexao->query("SELECT COUNT(*) as total FROM Pedidos WHERE DATE_FORMAT(data_pedido, '%Y-%m') = '$currentMonth' $filter");
    $currentTotal = $resultCurrent->fetch_assoc()['total'] ?? 0;
    $previousMonth = date("Y-m", strtotime("first day of last month"));
    $resultPrevious = $conexao->query("SELECT COUNT(*) as total FROM Pedidos WHERE DATE_FORMAT(data_pedido, '%Y-%m') = '$previousMonth' $filter");
    $previousTotal = $resultPrevious->fetch_assoc()['total'] ?? 0;
    $change = 0;
    if ($previousTotal > 0) {
    $change = round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2);
    } else {
    $change = $currentTotal > 0 ? 100 : 0;
    }

    // Relatório Horários: Pedidos por Hora
    $resultHour = $conexao->query("SELECT HOUR(data_pedido) as hora, COUNT(*) as total FROM Pedidos WHERE 1 $filter GROUP BY hora ORDER BY hora");
    $pedidosPorHora = [];
    while ($row = $resultHour->fetch_assoc()){
        $pedidosPorHora[] = $row;
    }

    // Relatório Cancelados: Lista de pedidos cancelados
    $resultCancelados = $conexao->query("
        SELECT p.id AS pedido_id, p.data_pedido, c.nome AS cliente_nome, s.nome AS servico_nome 
        FROM Pedidos p 
        INNER JOIN Cliente c ON p.id_cliente = c.id_cliente 
        INNER JOIN Servicos s ON p.id_servico = s.id_servico 
        WHERE p.estado = 'Cancelado' $filter 
        ORDER BY p.data_pedido DESC
    ");
    $cancelados = [];
    while ($row = $resultCancelados->fetch_assoc()){
        $cancelados[] = $row;
    }

    // Relatório Tendências: Últimos 12 meses (para o gráfico de linha)
    $twelveMonths = [];
    $totals12 = [];
    foreach ($pedidosPorMes as $row) {
        $twelveMonths[] = $row['mes'];
        $totals12[] = $row['total'];
    }

    // Relatório Entrega por Serviço: Tempo médio de entrega (em horas) por serviço para pedidos concluídos
    $resultAvgDeliveryService = $conexao->query("
        SELECT s.nome AS servico_nome, AVG(TIMESTAMPDIFF(HOUR, p.data_pedido, p.data_entrega)) as avg_hours
        FROM Pedidos p
        INNER JOIN Servicos s ON p.id_servico = s.id_servico
        WHERE p.estado = 'Concluído' AND p.data_entrega IS NOT NULL $filter
        GROUP BY s.id_servico
        ORDER BY avg_hours DESC
    ");
    $avgDeliveryService = [];
    while ($row = $resultAvgDeliveryService->fetch_assoc()){
        $avgDeliveryService[] = $row;
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Geovane Services</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <!-- Carrega jsPDF e html2canvas para exportação em PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
    /* Geral */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 0;
        }
        /* Topbar */
        .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #343a40;
        color: #fff;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1100;
        height: 120px;
        }
        .topbar .logo img {
            width: 150px; /* Reduzindo de 150px para 100px */
            height: 100px;
            border-radius: 5%;
        }
        .topbar .user-info {
        display: flex;
        align-items: center;
        }
        .topbar .user-info img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        }
        /* Sidebar */
        .sidebar {
        width: 250px;
        background: #212529;
        position: fixed;
        top: 120px; /* abaixo da topbar */
        bottom: 0;
        left: 0;
        padding: 20px 0;
        transition: width 0.3s;
        overflow-x: hidden;
        z-index: 1050;
        }
        .sidebar.collapsed {
        width: 80px;
        }
        .sidebar a {
        display: flex;
        align-items: center;
        color: #adb5bd;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 18px;
        transition: background 0.3s, color 0.3s;
        }
        .sidebar a.active, .sidebar a:hover {
        background: #495057;
        color: #fff;
        }
        .sidebar a i {
        font-size: 1.5rem;
        margin-right: 15px;
        transition: margin 0.3s;
        
        }
        .sidebar.collapsed a .menu-text {
        display: none;
        }
        .sidebar.collapsed a i {
        margin-right: 0;
        text-align: center;
        width: 100%;
        }
        /* Conteúdo */
        .content {
        margin-top: 50px;
        margin-left: 250px;
        padding: 100px 30px 30px;
        transition: margin-left 0.3s;
        }
        .sidebar.collapsed ~ .content {
        margin-left: 80px;
        }
        /* Botão de Toggle */
        .toggle-btn {
        position: fixed;
        top: 130px; /* abaixo da topbar */
        left: 260px;
        z-index: 1200;
        background: #212529;
        color: #fff;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        transition: left 0.3s;
        }
        .sidebar.collapsed ~ .toggle-btn {
        left: 90px;
        }
        .print-btn {
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        /* Abas */
        .nav-tabs .nav-link {
            font-size: 18px;
        }
        .tab-content {
            margin-top: 20px;
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
        #statusChart { width: 300px; height: 300px; }
        #mesChart { width: 300px; height: 200px; }
        #revenueChart { width: 300px; height: 200px; }
        #weekdayChart { width: 300px; height: 200px; }
        #hourChart { width: 300px; height: 200px; }
        #tendenciasChart { width: 300px; height: 200px; }
    </style>
</head>
<body>
   <!-- Topbar -->
   <div class="topbar">
    <div class="logo">
      <img src="../assets/logo.png" alt="Logo">
    </div>
    <div class="search-box">
      <form action="search.php" method="GET" class="d-flex">
        <input type="text" name="query" placeholder="Pesquisar..." class="form-control me-2">
        <button type="submit" class="btn btn-outline-light"><i class="bi bi-search"></i></button>
      </form>
    </div>
    <div class="user-info">
    <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $imagemPerfil; ?>" alt="Perfil"></a>
      
      <span><?php echo $nomeUsuario; ?></span>
      <a href="logout.php" class="ms-3 text-white"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </div>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
  <a href="admin.php" title="Principal"><i class="bi bi-house"></i><span class="menu-text">Principal</span></a>

    <a href="dashboard_admin.php"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
    <!-- <a href="chat_funcionario.php"><i class="bi bi-chat-left-text"></i><span class="menu-text">Mensagens</span></a> -->
    <a href="enviar_notificacao.php"><i class="bi bi-bell"></i><span class="menu-text">Notificações</span></a>
    <a href="admin_tickets.php"><i class="bi bi-ticket-perforated"></i><span class="menu-text">Tickets</span></a>
    <a href="gerar_voucher.php"><i class="bi bi-ticket-detailed"></i><span class="menu-text">Voucher</span></a>
    <a href="transacoes_admin.php"><i class="bi bi-currency-exchange"></i><span class="menu-text">Transações</span></a>
    <a href="saldo_clientes.php"><i class="bi bi-wallet2"></i><span class="menu-text">Saldo Clientes</span></a>
    <!-- <a href="movimentos_cliente.php"><i class="bi bi-bar-chart"></i><span class="menu-text">Movimentos</span></a> -->
    <a href="exibirServicos.php"><i class="bi bi-tools"></i><span class="menu-text">Serviços</span></a>
    <a href="cadastrarServico.php"><i class="bi bi-plus-circle"></i><span class="menu-text">Registrar Serviços</span></a>
    <a href="registroFun.php"><i class="bi bi-person-plus"></i><span class="menu-text">Registrar Funcionário</span></a>
    <a href="registroCliente.php"><i class="bi bi-person-plus"></i><span class="menu-text">Registrar Cliente</span></a>
    <a href="controlFunci.php"><i class="bi bi-people"></i><span class="menu-text">Controle Funcionário</span></a>
    <a href="controlCliente.php"><i class="bi bi-people"></i><span class="menu-text">Controle Clientes</span></a>
    <a href="pedidos.php"><i class="bi bi-receipt"></i><span class="menu-text">Pedidos</span></a>
    <a href="perfil.php"><i class="bi bi-gear"></i><span class="menu-text">Configurações</span></a>
    <a href="permissoes.php" title="Permissões"><i class="bi bi-shield-lock"></i><span class="menu-text">Permissões</span></a>

    <a href="relatorios.php"><i class="bi bi-file-earmark-text"></i><span class="menu-text">Relatórios</span></a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span class="menu-text">Sair</span></a>
  </div>
  <!-- Botão de Toggle -->
  <button class="toggle-btn" id="toggleBtn"><i class="bi bi-chevron-left"></i></button>

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
        <!-- Abas (Tabs) -->
        <ul class="nav nav-tabs" id="relatorioTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Visão Geral</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button" role="tab" aria-controls="pedidos" aria-selected="false">Pedidos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button" role="tab" aria-controls="clientes" aria-selected="false">Clientes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="funcionarios-tab" data-bs-toggle="tab" data-bs-target="#funcionarios" type="button" role="tab" aria-controls="funcionarios" aria-selected="false">Funcionários</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="detalhes-tab" data-bs-toggle="tab" data-bs-target="#detalhes" type="button" role="tab" aria-controls="detalhes" aria-selected="false">Análise Detalhada</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="desempenho-tab" data-bs-toggle="tab" data-bs-target="#desempenho" type="button" role="tab" aria-controls="desempenho" aria-selected="false">Desempenho</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="exportar-tab" data-bs-toggle="tab" data-bs-target="#exportar" type="button" role="tab" aria-controls="exportar" aria-selected="false">Exportar</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="graficos-tab" data-bs-toggle="tab" data-bs-target="#graficos" type="button" role="tab" aria-controls="graficos" aria-selected="false">Gráficos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="horarios-tab" data-bs-toggle="tab" data-bs-target="#horarios" type="button" role="tab" aria-controls="horarios" aria-selected="false">Horários</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelados-tab" data-bs-toggle="tab" data-bs-target="#cancelados" type="button" role="tab" aria-controls="cancelados" aria-selected="false">Cancelados</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tendencias-tab" data-bs-toggle="tab" data-bs-target="#tendencias" type="button" role="tab" aria-controls="tendencias" aria-selected="false">Tendências</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="entrega-tab" data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab" aria-controls="entrega" aria-selected="false">Entrega por Serviço</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pdf-tab" data-bs-toggle="tab" data-bs-target="#pdf" type="button" role="tab" aria-controls="pdf" aria-selected="false">Exportar PDF</button>
            </li>
        </ul>
        <div class="tab-content" id="relatorioTabsContent">
            <!-- Visão Geral -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="report-section">
                    <h3>Visão Geral</h3>
                    <p><strong>Total de Pedidos:</strong> <?php echo $totalPedidos; ?></p>
                    <p><strong>Média dos Preços:</strong> <?php echo number_format($avgPreco, 2, ',', '.'); ?> Kz</p>
                    <p><strong>Receita Total (Concluídos):</strong> <?php echo number_format($receitaTotal, 2, ',', '.'); ?> Kz</p>
                    <p><strong>Taxa de Conclusão:</strong> <?php echo $taxaConclusao; ?>%</p>
                    <p><strong>Valor Médio dos Pedidos Concluídos:</strong> <?php echo number_format($valorMedio, 2, ',', '.'); ?> Kz</p>
                </div>

                
            </div>
            <!-- Pedidos -->
            <div class="tab-pane fade" id="pedidos" role="tabpanel" aria-labelledby="pedidos-tab">
                <div class="report-section">
                    <h3>Detalhes dos Pedidos</h3>
                    <?php if ($firstPedido): ?>
                        <p><strong>Primeiro Pedido (mais antigo):</strong></p>
                        <p>ID: <?php echo $firstPedido['pedido_id']; ?></p>
                        <p>Cliente: <?php echo htmlspecialchars($firstPedido['cliente_nome'] ?? ''); ?></p>
                        <p>Serviço: <?php echo htmlspecialchars($firstPedido['servico_nome'] ?? ''); ?></p>
                        <p>Data do Pedido: <?php echo date("d/m/Y H:i", strtotime($firstPedido['data_pedido'] ?? '')); ?></p>
                    <?php else: ?>
                        <p>Nenhum pedido encontrado.</p>
                    <?php endif; ?>
                    <hr>
                    <?php if ($lastPedido): ?>
                        <p><strong>Último Pedido (mais recente):</strong></p>
                        <p>ID: <?php echo $lastPedido['pedido_id']; ?></p>
                        <p>Cliente: <?php echo htmlspecialchars($lastPedido['cliente_nome'] ?? ''); ?></p>
                        <p>Serviço: <?php echo htmlspecialchars($lastPedido['servico_nome'] ?? ''); ?></p>
                        <p>Data do Pedido: <?php echo date("d/m/Y H:i", strtotime($lastPedido['data_pedido'] ?? '')); ?></p>
                    <?php else: ?>
                        <p>Nenhum pedido encontrado.</p>
                    <?php endif; ?>
                    <hr>
                    <?php if ($recentDelivery): ?>
                        <p><strong>Pedido com Entrega Mais Recente:</strong></p>
                        <p>ID: <?php echo $recentDelivery['pedido_id']; ?></p>
                        <p>Cliente: <?php echo htmlspecialchars($recentDelivery['cliente_nome'] ?? ''); ?></p>
                        <p>Serviço: <?php echo htmlspecialchars($recentDelivery['servico_nome'] ?? ''); ?></p>
                        <p>Data de Entrega: <?php echo date("d/m/Y", strtotime($recentDelivery['data_entrega'] ?? '')); ?></p>
                    <?php else: ?>
                        <p>Nenhum pedido com data de entrega definida.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Clientes -->
            <div class="tab-pane fade" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">
                <div class="report-section">
                    <h3>Top 10 Clientes Detalhados</h3>
                    <?php if (!empty($clientDetails)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID do Cliente</th>
                                    <th>Nome</th>
                                    <th>Total de Pedidos</th>
                                    <th>Média do Preço</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientDetails as $client): ?>
                                    <tr>
                                        <td><?php echo $client['id_cliente']; ?></td>
                                        <td><?php echo htmlspecialchars($client['nome'] . ' ' . $client['sobrenome']); ?></td>
                                        <td><?php echo $client['total_pedidos']; ?></td>
                                        <td><?php echo number_format($client['media_preco'], 2, ',', '.'); ?> Kz</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
            </div>
           
            <div class="tab-pane fade" id="funcionarios" role="tabpanel" aria-labelledby="funcionarios-tab">
                <div class="report-section">
                    <h3>Relatório Detalhado: Clientes Atendidos por Funcionário</h3>
                    <?php if (!empty($relatorio)): ?>
                        <?php foreach ($relatorio as $id => $dados): ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Funcionário:</strong> <?php echo htmlspecialchars($dados['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Clientes Atendidos</h5>
                                    <?php if (!empty($dados['clientes'])): ?>
                                        <ul>
                                            <?php foreach ($dados['clientes'] as $cliente): ?>
                                                <li>
                                                    <?php echo htmlspecialchars($cliente['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                                    (<?php echo $cliente['total']; ?> atendimentos)
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p style="color: red;">Nenhum cliente registrado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhum funcionário encontrado.</p>
                    <?php endif; ?>
                </div>

            </div>


            <!-- Análise Detalhada -->
            <div class="tab-pane fade" id="detalhes" role="tabpanel" aria-labelledby="detalhes-tab">
                <div class="report-section">
                    <h3>Análise Detalhada</h3>
                    <p><strong>Tempo Médio de Entrega:</strong> <?php echo round($avgDeliveryHours, 2); ?> horas (<?php echo round($avgDeliveryDays, 2); ?> dias)</p>
                    <!-- Outras análises podem ser adicionadas aqui -->
                </div>
            </div>
            <!-- Desempenho -->
            <div class="tab-pane fade" id="desempenho" role="tabpanel" aria-labelledby="desempenho-tab">
                <div class="report-section">
                    <h3>Pedidos por Dia da Semana</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Dia da Semana</th>
                                <th>Total de Pedidos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidosWeekday as $row): ?>
                                <tr>
                                    <td><?php echo $weekDays[$row['weekday']]; ?></td>
                                    <td><?php echo $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <canvas id="weekdayChart" width="300" height="200"></canvas>
                </div>
                <div class="report-section">
                    <h3>Pedidos Pendentes há Mais de 7 Dias</h3>
                    <?php if (!empty($pending7)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID do Pedido</th>
                                    <th>Data do Pedido</th>
                                    <th>Cliente</th>
                                    <th>Serviço</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending7 as $pend): ?>
                                    <tr>
                                        <td><?php echo $pend['pedido_id']; ?></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($pend['data_pedido'])); ?></td>
                                        <td><?php echo htmlspecialchars($pend['cliente_nome'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($pend['servico_nome'] ?? ''); ?></td>
                                        <td><?php echo $pend['estado']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum pedido pendente há mais de 7 dias.</p>
                    <?php endif; ?>
                </div>
                <div class="report-section">
                    <h3>Comparação Mensal</h3>
                    <p><strong>Mês Atual (<?php echo $currentMonth; ?>):</strong> <?php echo $currentTotal; ?> pedidos</p>
                    <p><strong>Mês Anterior (<?php echo $previousMonth; ?>):</strong> <?php echo $previousTotal; ?> pedidos</p>
                    <p><strong>Variação:</strong> <?php echo $change; ?>%</p>
                </div>
            </div>
            <!-- Exportar (CSV) -->
            <div class="tab-pane fade" id="exportar" role="tabpanel" aria-labelledby="exportar-tab">
                <div class="report-section">
                    <h3>Exportar Relatório (CSV)</h3>
                    <p>Clique no botão abaixo para exportar os dados filtrados em formato CSV.</p>
                    <a href="export_relatorio.php?start_date=<?php echo $_GET['start_date'] ?? ''; ?>&end_date=<?php echo $_GET['end_date'] ?? ''; ?>" class="btn btn-success">Exportar CSV</a>
                </div>
            </div>

            <!-- Horários -->
            <div class="tab-pane fade" id="horarios" role="tabpanel" aria-labelledby="horarios-tab">
                <div class="report-section">
                    <h3>Pedidos por Hora</h3>
                    <canvas id="hourChart" width="300" height="200"></canvas>
                </div>
            </div>
            <!-- Cancelados -->
            <div class="tab-pane fade" id="cancelados" role="tabpanel" aria-labelledby="cancelados-tab">
                <div class="report-section">
                    <h3>Pedidos Cancelados</h3>
                    <?php if (!empty($cancelados)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID do Pedido</th>
                                    <th>Data do Pedido</th>
                                    <th>Cliente</th>
                                    <th>Serviço</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cancelados as $cancelado): ?>
                                    <tr>
                                        <td><?php echo $cancelado['pedido_id']; ?></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($cancelado['data_pedido'])); ?></td>
                                        <td><?php echo htmlspecialchars($cancelado['cliente_nome'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($cancelado['servico_nome'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum pedido cancelado encontrado.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Tendências -->
            <div class="tab-pane fade" id="tendencias" role="tabpanel" aria-labelledby="tendencias-tab">
                <div class="report-section">
                    <h3>Tendências dos Pedidos (Últimos 12 Meses)</h3>
                    <canvas id="tendenciasChart" width="300" height="200"></canvas>
                </div>
            </div>
            <!-- Entrega por Serviço -->
            <div class="tab-pane fade" id="entrega" role="tabpanel" aria-labelledby="entrega-tab">
                <div class="report-section">
                    <h3>Tempo Médio de Entrega por Serviço</h3>
                    <?php if (!empty($avgDeliveryService)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Tempo Médio (horas)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($avgDeliveryService as $service): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($service['servico_nome'] ?? ''); ?></td>
                                        <td><?php echo round($service['avg_hours'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum dado disponível para entrega por serviço.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Exportar PDF -->
            <div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
                <div class="report-section">
                    <h3>Exportar Relatório para PDF</h3>
                    <p>Clique no botão abaixo para exportar o relatório exibido para PDF.</p>
                    <button id="exportPdfBtn" class="btn btn-danger">Exportar para PDF</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                        ticks: { precision: 0 }
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
                        ticks: { precision: 0 }
                    }
                }
            }
        });
        
        // Gráfico de Barras: Pedidos por Hora
        var pedidosPorHora = <?php echo json_encode($pedidosPorHora); ?>;
        var horas = [];
        var totalHora = [];
        pedidosPorHora.forEach(function(item) {
            horas.push(item.hora);
            totalHora.push(item.total);
        });
        var ctx4 = document.getElementById('hourChart').getContext('2d');
        var hourChart = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: horas,
                datasets: [{
                    label: 'Pedidos por Hora',
                    data: totalHora,
                    backgroundColor: '#ff6384'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pedidos por Hora'
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Hora do Dia' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
        
        // Gráfico de Linha: Tendências dos Pedidos (Últimos 12 Meses)
        var ctx5 = document.getElementById('tendenciasChart').getContext('2d');
        var tendenciasChart = new Chart(ctx5, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Pedidos',
                    data: totalMes,
                    borderColor: '#00acc1',
                    backgroundColor: 'rgba(0, 172, 193, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tendências dos Pedidos (Últimos 12 Meses)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
        
        // Gráfico de Linha: Tendências de Entrega por Serviço (Tempo médio)
        // Opcional: Você pode criar um gráfico semelhante se desejar visualizar as métricas de entrega
        // Atualize se necessário.

        // Atualiza os gráficos quando as abas são trocadas
        var triggerTabList = [].slice.call(document.querySelectorAll('#relatorioTabs button'));
        triggerTabList.forEach(function(triggerEl) {
            triggerEl.addEventListener('shown.bs.tab', function (event) {
                statusChart.resize();
                mesChart.resize();
                revenueChart.resize();
                hourChart.resize();
                if(document.getElementById('tendenciasChart')){
                    tendenciasChart.resize();
                }
            });
        });

        // Exportar PDF utilizando jsPDF e html2canvas
        document.getElementById('exportPdfBtn') && document.getElementById('exportPdfBtn').addEventListener('click', function() {
            html2canvas(document.querySelector('.content')).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
                const imgProps= pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save("relatorio_<?php echo date('Ymd_His'); ?>.pdf");
            });
        });
    </script>

    <script>
        // Toggle da sidebar
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        this.innerHTML = sidebar.classList.contains('collapsed') 
            ? '<i class="bi bi-chevron-right"></i>' 
            : '<i class="bi bi-chevron-left"></i>';
        });
        
        const links = document.querySelectorAll('.sidebar a');
        const currentPage = window.location.pathname.split('/').pop(); // Pega o nome do arquivo atual

        links.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

    </script> 
</body>
</html>
