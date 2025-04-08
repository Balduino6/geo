<?php
require 'verifyadm.php';
require '../conexao.php';

// Dados do funcionário (foto de perfil)
$id_funcionario = $_SESSION['id_funcionario'];
$sql = "SELECT imagem_perfil FROM Funcionario WHERE id_funcionario = $id_funcionario";
$result = $conexao->query($sql);
$funcionario = $result->fetch_assoc();
$imagemPerfil = $funcionario['imagem_perfil'];

// Consultas para os cards de resumo
$sqlPedidos = "SELECT COUNT(*) as total_pedidos FROM Pedidos";
$resPedidos = $conexao->query($sqlPedidos);
$totalPedidos = $resPedidos->fetch_assoc()['total_pedidos'];

$sqlCarregamentos = "SELECT IFNULL(SUM(valor),0) as total_carregamentos FROM transacoes WHERE tipo = 'carregamento' AND status = 'confirmado'";
$resCarregamentos = $conexao->query($sqlCarregamentos);
$totalCarregamentos = $resCarregamentos->fetch_assoc()['total_carregamentos'];

$sqlPedidosValor = "SELECT IFNULL(SUM(valor),0) as total_pedidos_valor FROM transacoes WHERE tipo = 'pedido' AND status = 'confirmado'";
$resPedidosValor = $conexao->query($sqlPedidosValor);
$totalPedidosValor = $resPedidosValor->fetch_assoc()['total_pedidos_valor'];

// Consulta para gráfico de transações mensais
$sqlChart = "SELECT DATE_FORMAT(data, '%Y-%m') as mes, IFNULL(SUM(valor), 0) as total 
             FROM transacoes 
             WHERE status = 'confirmado' 
             GROUP BY mes 
             ORDER BY mes";
$resChart = $conexao->query($sqlChart);
$chartLabels = [];
$chartData = [];
while ($row = $resChart->fetch_assoc()) {
    $chartLabels[] = $row['mes'];
    $chartData[] = $row['total'];
}
$chartLabels = json_encode($chartLabels);
$chartData = json_encode($chartData);

// Nome do usuário (de sessão)
$nomeUsuario = $_SESSION['usuario'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Administrativo - Geovane Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
      flex-wrap: wrap;
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
      width: 150px;
      height: 100px;
      border-radius: 5%;
    }
    /* Área de Pesquisa */
    .search-box {
      position: relative;
      display: flex;
      align-items: center;
    }
    .search-box .search-input {
      width: 0;
      opacity: 0;
      transition: width 0.3s ease, opacity 0.3s ease;
      border: none;
      border-radius: 5px;
      padding: 5px;
      margin-left: 10px;
    }
    .search-box.active .search-input {
      width: 200px;
      opacity: 1;
    }
    /* Dados do usuário */
    .topbar .user-info {
      display: flex;
      align-items: center;
    }
    .topbar .user-info a {
      text-decoration: none;
      font-size: 18px;
      color: #fff;
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
      top: 120px; /* logo e topbar */
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
    /* Cards de Resumo */
    .card-summary {
      background: linear-gradient(135deg, rgb(37,55,83), rgb(3,21,48));
      color: #fff;
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      text-align: center;
      padding: 20px;
    }
    .card-summary:hover {
      transform: translateY(-5px);
    }
    .card-summary h5 {
      font-size: 1.2rem;
      margin-bottom: 10px;
    }
    .card-summary p {
      font-size: 1.5rem;
      font-weight: bold;
      margin: 0;
    }
    /* Gráfico */
    .chart-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      margin-top: 30px;
    }
    /* Rodapé */
    .cop {
      color: #444;
      text-align: center;
      padding: 20px;
      width: 100%;
      border-top: solid 1px #ddd;
    }
    /* Responsividade para dispositivos móveis */
    @media (max-width: 768px) {
      .content {
        margin-left: 80px; /* Mantém espaço para a sidebar colapsada */
        padding: 90px 15px 15px;
      }
      .sidebar {
        display: block; /* Não ocultar a sidebar; ela já está colapsada */
      }
      .toggle-btn {
        left: 90px;
      }
      .topbar {
        justify-content: space-around;
        height: auto;
        padding: 10px;
      }
      .topbar .logo img {
        width: 120px;
        height: auto;
      }
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <div class="topbar">
    <div class="logo">
      <img src="../assets/logo.png" alt="Logo">
    </div>
    <!-- Caixa de Pesquisa: inicialmente apenas o ícone -->
    <div class="search-box" id="searchBox">
      <i class="bi bi-search search-icon" style="cursor:pointer; font-size:1.5rem;"></i>
      <form action="search.php" method="GET" class="d-flex align-items-center ms-2">
        <input type="text" name="query" placeholder="Pesquisar..." class="form-control search-input">
      </form>
    </div>
    <!-- Dados do Usuário -->
    <div class="user-info">
      <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $imagemPerfil; ?>" alt="Perfil"></a>
      <span><?php echo $nomeUsuario; ?></span>
      <a href="logout.php" class="ms-3 text-white"> Sair <i class="bi bi-box-arrow-right"></i></a>
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
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card card-summary">
            <div class="card-body">
              <h5>Total de Pedidos</h5>
              <p><?php echo $totalPedidos; ?></p>
              <i class="bi bi-receipt" style="font-size: 2rem;"></i>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-summary">
            <div class="card-body">
              <h5>Total de Carregamentos</h5>
              <p>KZ <?php echo number_format($totalCarregamentos, 2, ',', '.'); ?></p>
              <i class="bi bi-wallet2" style="font-size: 2rem;"></i>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-summary">
            <div class="card-body">
              <h5>Total de Débitos</h5>
              <p>KZ <?php echo number_format($totalPedidosValor, 2, ',', '.'); ?></p>
              <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
            </div>
          </div>
        </div>
      </div>
      <!-- Card do Gráfico -->
      <div class="card chart-card mt-4 p-3">
        <h5 class="text-center">Transações Mensais</h5>
        <canvas id="chartTransacoes" width="400" height="200"></canvas>
      </div>
      <!-- (Opcional) Tabela de Pedidos Recentes -->
      <div class="card mt-4 p-3">
        <h5 class="text-center">Pedidos Recentes</h5>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Serviço</th>
              <th>Categoria</th>
              <th>Status</th>
              <th>Atendido por</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Exemplo: recuperar 5 pedidos recentes
            $sqlRecent = "SELECT p.id AS pedido_id, c.nome AS cliente_nome, s.nome AS servico_nome, cat.nome AS categoria_nome, p.estado, f.nome AS funcionario_nome 
                         FROM Pedidos p
                         INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
                         INNER JOIN Servicos s ON p.id_servico = s.id_servico
                         LEFT JOIN categorias cat ON s.id_categoria = cat.id_categoria
                         LEFT JOIN Funcionario f ON p.id_funcionario = f.id_funcionario
                         ORDER BY p.data_pedido DESC LIMIT 5";
            $resRecent = $conexao->query($sqlRecent);
            while($row = $resRecent->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo $row['pedido_id']; ?></td>
              <td><?php echo $row['cliente_nome']; ?></td>
              <td><?php echo $row['servico_nome']; ?></td>
              <td><?php echo !empty($row['categoria_nome']) ? $row['categoria_nome'] : "Sem categoria"; ?></td>
              <td><?php echo $row['estado']; ?></td>
              <td><?php echo !empty($row['funcionario_nome']) ? $row['funcionario_nome'] : "Não atendido"; ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="cop">
    <p class="copy">© Todos direitos reservados por GeovaneServices</p>
  </div>

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
    
    // Ativa tooltip (se necessário) – certifique-se de carregar o Bootstrap JS se for usar
    const links = document.querySelectorAll('.sidebar a');
    const currentPage = window.location.pathname.split('/').pop(); // pega o nome do arquivo atual
    links.forEach(link => {
      const linkPage = link.getAttribute('href').split('/').pop();
      if (linkPage === currentPage) {
        link.classList.add('active');
      }
    });
    
    // Toggle da pesquisa: clique no ícone revela o campo
    const searchBox = document.getElementById('searchBox');
    const searchIcon = searchBox.querySelector('.search-icon');
    const searchInput = searchBox.querySelector('.search-input');
    
    searchIcon.addEventListener('click', function() {
      searchBox.classList.toggle('active');
      // Se ativo, foca no campo; se não, limpa o valor
      if(searchBox.classList.contains('active')) {
        searchInput.focus();
      } else {
        searchInput.value = '';
      }
    });

    // Inicializa o gráfico de transações
    const canvas = document.getElementById('chartTransacoes');
    if(canvas) {
      const ctx = canvas.getContext('2d');
      const chartTransacoes = new Chart(ctx, {
        type: 'line',
        data: {
          labels: <?php echo $chartLabels; ?>,
          datasets: [{
            label: 'Total (KZ)',
            data: <?php echo $chartData; ?>,
            backgroundColor: 'rgba(0, 172, 193, 0.2)',
            borderColor: 'rgba(0, 172, 193, 1)',
            borderWidth: 2,
            fill: true
          }]
        },
        options: {
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    } else {
      console.error("Elemento canvas #chartTransacoes não encontrado!");
    }
  </script>
</body>
</html>
