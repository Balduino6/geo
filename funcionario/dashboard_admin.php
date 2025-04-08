<?php

require './veryFun.php';
require '../conexao.php'; // Usa a conexão via mysqli

    // Supondo que as permissões já foram carregadas na sessão após o login:
      $menu_permissions = $_SESSION['menu_permissions'] ?? [];

      // Defina os itens disponíveis com a mesma chave padronizada.
      $available_menus = [
          "dashboard"            => '<a href="funcionario_dashboard.php"><img src="../assets/dashboard.png" alt="" class="icon" style="width: 30px;">Dashboard</a>',
          "chat_funcionario"     => '<a href="chat_funcionario.php"><img src="../assets/chat.png" alt="" class="icon" style="width: 30px;">Mensagens</a>',
          "enviar_notificacoes"  => '<a href="enviar_notificacao.php"><img src="../assets/notification.png" alt="" class="icon" style="width: 30px;">Enviar Notificações</a>',
          "tickets"              => '<a href="admin_tickets.php"><img src="../assets/tickets.png" alt="" class="icon" style="width: 30px;">Tickets</a>',
          "transacoes_admin"     => '<a href="transacoes_admin.php"><img src="../assets/transacoes.png" alt="" class="icon" style="width: 30px;">Transações</a>',
          "saldo_clientes"       => '<a href="saldo_clientes.php"><img src="../assets/saldo.png" alt="" class="icon" style="width: 30px;">Saldo dos Clientes</a>',
          "controlFunci"         => '<a href="controlFunci.php"><img src="../assets/employee.png" alt="" class="icon" style="width: 30px;">Controle de Funcionário</a>',
          "controlCliente"       => '<a href="controlCliente.php"><img src="../assets/customer.png" alt="" class="icon" style="width: 30px;">Controle de Clientes</a>',
          "exibirServicos"       => '<a href="exibirServicos.php"><img src="../assets/services.png" alt="" class="icon" style="width: 30px;">Ver Serviços</a>',
          "cadastrarServico"     => '<a href="cadastrarServico.php"><img src="../assets/registration.png" alt="" class="icon" style="width: 30px; color:#f5f5f5;">Registrar Serviços</a>',
          "registroFun"          => '<a href="registroFun.php"><img src="../assets/regFun.png" alt="" class="icon" style="width: 30px;">Registrar Funcionário</a>',
          "registroCliente"      => '<a href="registroCliente.php"><img src="../assets/regCli.png" alt="" class="icon" style="width: 30px;">Registrar  Cliente</a>',
          "relatorios"           => '<a href="relatorios.php"><img src="../assets/relatorio.png" alt="" class="icon" style="width: 30px;">Relatorios</a>',
          "gerar_voucher"        => '<a href="gerar_voucher.php"><img src="../assets/voucher.png" alt="" class="icon" style="width: 30px;">Gerar Voucher</a>',
          "pedidos"              => '<a href="pedidos.php"><img src="../assets/clienteP.png" alt="" class="icon" style="width: 30px;">Pedidos de Serviços</a>',
          "configuracao"         => '<a href="perfil.php" style="color: white;"><img src="../assets/service.png" alt="" class="icon" style="width: 30px;">Configurações</a>',
          "permissoes"           => '<a href="permissoes.php" style="color: white;">Permissões</a>',
          "movimentos_cliente"   => '<a href="movimentos_cliente.php?"><img src="../assets/debit.png" alt="" class="icon" style="width: 30px;">Movimentos de um Cliente</a>',
          "ver_notificacoes"     => '<a href="ver_notificacoes.php"><img src="../assets/notifications.png" alt="" class="icon" style="width: 30px;">Ver Notificações</a>',
      ];
  
      // Atualiza as permissões na sessão a partir do banco
      $id_funcionario = $_SESSION['id_funcionario'];
      $query = "SELECT menu_permissions FROM funcionario WHERE id_funcionario = ?";
      $stmt = $conexao->prepare($query);
      $stmt->bind_param("i", $id_funcionario);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
          $_SESSION['menu_permissions'] = json_decode($row['menu_permissions'], true);
      } else {
          $_SESSION['menu_permissions'] = [];
      }
  
      // Atualiza a variável local com as permissões recuperadas
      $menu_permissions = $_SESSION['menu_permissions'];
  
      // Após a verificação de sessão, logo após o login:
      $id_funcionario = $_SESSION['id_funcionario'];
      $query = "SELECT menu_permissions FROM funcionario WHERE id_funcionario = ?";
      $stmt = $conexao->prepare($query);
      $stmt->bind_param("i", $id_funcionario);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
          // Armazena as permissões como array na sessão:
          $_SESSION['menu_permissions'] = json_decode($row['menu_permissions'], true);
      } else {
          $_SESSION['menu_permissions'] = [];
      }


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

// Consulta total de pedidos
$sqlPedidos = "SELECT COUNT(*) as total_pedidos FROM Pedidos";
$resPedidos = $conexao->query($sqlPedidos);
$rowPedidos = $resPedidos->fetch_assoc();
$totalPedidos = $rowPedidos['total_pedidos'];

// Consulta total de carregamentos (apenas confirmados)
$sqlCarregamentos = "SELECT IFNULL(SUM(valor),0) as total_carregamentos FROM transacoes WHERE tipo = 'carregamento' AND status = 'confirmado'";
$resCarregamentos = $conexao->query($sqlCarregamentos);
$rowCarregamentos = $resCarregamentos->fetch_assoc();
$totalCarregamentos = $rowCarregamentos['total_carregamentos'];

// Consulta total de débitos (pedidos confirmados)
$sqlPedidosValor = "SELECT IFNULL(SUM(valor),0) as total_pedidos_valor FROM transacoes WHERE tipo = 'pedido' AND status = 'confirmado'";
$resPedidosValor = $conexao->query($sqlPedidosValor);
$rowPedidosValor = $resPedidosValor->fetch_assoc();
$totalPedidosValor = $rowPedidosValor['total_pedidos_valor'];

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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Administrativo - Geovane Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f1f1;
    }
    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        height: 120px;
        background-color: #333;
        color: white;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        z-index: 1000;

    }

    .search-box {
        flex-grow: 1; /* Faz a caixa de pesquisa crescer para ocupar o espaço central */
        display: flex;
        justify-content: center; /* Centraliza o conteúdo da caixa de pesquisa */
        margin: 0 20px; /* Margem para afastar dos outros elementos */
    }

    .search-box form {
        display: flex;
        width: 50%; /* Define uma largura fixa ou relativa para a caixa de pesquisa */
    }

    .search-box input[type="text"] {
        flex-grow: 1;
        padding: 12px;
        font-size: 16px;
        border: 2px solid #444;
        border-radius: 4px 0 0 4px;
        background-color: #575757;
        color: white;
    }

    .search-box button {
        padding: 12px 20px;
        border: 2px solid #575757;
        border-radius: 0 4px 4px 0;
        background-color: #444;
        color: white;
        cursor: pointer;
    }

    .topbar .search-box button:hover {
        background-color: #575757; /* Cor mais escura no hover */
        border-color: #444; /* Sincroniza a cor da borda no hover */
    }

    .topbar .user-info {
        display: flex;
        align-items: center; /* Centraliza verticalmente */
        margin-left: auto; /* Empurra para a direita */
        margin-right: 20px;
    }

    .topbar .user-info .username {
        margin-right: 15px; /* Espaçamento entre nome e o botão de sair */
        font-size: 18px;
        color: white;
    }

    .topbar .user-info a {

        color: whitesmoke;
        text-decoration: none;
        font-size: 25px;
        transition: color 0.3s;
        padding: 1px 3px;
        border-radius: 5%;
        margin-right: 30px;
        display: flex;

        align-items: center;
    }

    .topbar .user-info a:hover{
        color: #ddd;
        /* border-top: 5px solid #ddd; */
        animation: 1s;
        transition: .1s;
    }

    .topbar .user-info a i{
        margin-left: 5px;
    }

    .perfil-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
    }

    .topbar .logo{
        width: 200px;
        /* height: 100px; */
        /* border: solid 1px red; */
        text-align: center;
        margin-left: 20px;      
    }

    .topbar .logo img {
        width: 185px;
        /* height: 100px; */
        border-radius: 5%;
        /* border: solid 1px red; */
    }

    .sidebar {
        width: 250px;
        background-color: #444;
        color: white;
        padding-top: 60px;
        position: fixed;
        height: 100%;
        top: 0;
        padding-left: 20px;
        padding-top: 80px;

        overflow-y: auto;
        transition: 2s;
    }

    .sidebar a {
        margin-top: 50px;
        text-decoration: none;
        color: white;
        display: flex;
        align-items: center;
        transition: 0.3s;
        margin-bottom: 5px;
        font-size: 20px;
        padding: 20px 4%;
    }

    .sidebar a .icon, .sidebar a .txt-icon {
        font-size: 30px;
        margin-right: 15px;
    }

    .sidebar a:hover {
        background-color: #575757;
        transition: 1s;
    }
    .content {
      margin-left: 250px;
      margin-top: 120px;
      padding: 20px;
      min-height: calc(100vh - 120px);
    }
    .cop {
        /* background-color: #222; */
        color: #444;
        text-align: center;
        padding: 20px;
        width: 100%;
        border-top: solid 1px #ddd;
    }
    .card-summary { margin-bottom: 20px; }
  </style>
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo";>
        </div>

        <!-- Caixa de Pesquisa -->
        <div class="search-box">
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Pesquisar no sistema...">
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="user-info">
            <div>
                <a href="perfil.php"><img src="../upload/imagens_perfil/<?php echo $funcionario['imagem_perfil']; ?>" alt="Perfil" class="perfil-img"></a>
                
            </div>
            <div class="username">Olá, <?php  echo  " $nomeUsuario"; ?> </div>

            <!-- <a href="perfil.php" style="margin-left: 10px;">Perfil</a> -->
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair<i class="bi bi-box-arrow-right"></i></a>
        </div>

    </div>

    <div class="sidebar">
    <a href="funcionario.php"> <span class="icon"><i class="bi bi-house"></i></span>
    <span class="txt-link">Principal</span></a>

    <?php
    // Exibe somente os itens para os quais o funcionário tem permissão.
    foreach ($available_menus as $menu_key => $menu_html) {
        if (in_array($menu_key, $menu_permissions)) {
            echo $menu_html;
        }
    }
    ?>
    
    <!-- Sempre pode exibir links comuns como "Sair" -->
    <a href="./logout.php" style="color: white; margin-left: 10px;">
        <img src="./assets/sair.png" alt="" style="width: 20px;">
        <span class="icon"><i class="bi bi-box-arrow-right"></i></span>Sair
    </a>
</div>
  <!-- Conteúdo -->
  <div class="content">
    <div class="container">
      <h2>Dashboard Administrativo</h2>
      <div class="row text-center">
        <div class="col-md-4 card-summary">
          <div class="card p-3">
            <h5>Total de Pedidos</h5>
            <p><?php echo $totalPedidos; ?></p>
          </div>
        </div>
        <div class="col-md-4 card-summary">
          <div class="card p-3">
            <h5>Total de Carregamentos</h5>
            <p>KZ <?php echo number_format($totalCarregamentos,2,',','.'); ?></p>
          </div>
        </div>
        <div class="col-md-4 card-summary">
          <div class="card p-3">
            <h5>Total de Débitos (Pedidos)</h5>
            <p>KZ <?php echo number_format($totalPedidosValor,2,',','.'); ?></p>
          </div>
        </div>
      </div>
      <!-- Gráfico de Transações Mensais --> 
      <div class="card mt-4 p-3">
         <h5>Transações Mensais</h5>
         <canvas id="chartTransacoes"></canvas>
      </div>
    </div>
  </div>
  <!-- Rodapé -->
  <div class="cop">
    <p>© Todos direitos reservados por GeovaneServices</p>
  </div>
  <script>
    const ctx = document.getElementById('chartTransacoes').getContext('2d');
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
  </script>
</body>
</html>
