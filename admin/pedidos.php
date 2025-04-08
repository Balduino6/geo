<?php
require './verifyadm.php';
require '../conexao.php';

$id_funcionario = $_SESSION['id_funcionario'];
// Busca os dados do funcionário, incluindo a imagem de perfil
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

// Consulta os pedidos dos clientes com os dados do serviço, do cliente, as datas e o funcionário que atendeu (se houver)
$query = "
    SELECT 
        p.id AS pedido_id, 
        c.nome AS cliente_nome, 
        s.nome AS servico_nome, 
        s.preco, 
        p.estado, 
        p.data_pedido, 
        p.data_entrega,
        f.nome AS funcionario_nome,
        cat.nome AS categoria_nome
    FROM Pedidos p
    INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    LEFT JOIN Funcionario f ON p.id_funcionario = f.id_funcionario
    LEFT JOIN categorias cat ON s.id_categoria = cat.id_categoria
    ORDER BY p.id DESC
";

$result = $conexao->query($query);
if (!$result) {
    die("Erro na query: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos dos Clientes - Geovane Services</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .table-status {
            background: white;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .cop {
            color: #444;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            width: 100%;
        }
        /* Indicador circular */
        .circular-chart {
            display: block;
            margin: 0 auto;
            max-width: 80px;
            max-height: 80px;
        }
        .circle-bg {
            fill: none;
            stroke: #eee;
            stroke-width: 3.8;
        }
        .circle {
            fill: none;
            stroke-width: 2.8;
            stroke-linecap: round;
            stroke: #00acc1;
            transition: stroke-dasharray 0.6s ease;
        }
        .percentage {
            font-size: 0.5em;
            text-anchor: middle;
            fill: #666;
        }
             /* Responsividade */
             @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
            .sidebar {
                display: none;
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
  <!-- <a href="admin.php" title="Principal"><i class="bi bi-house"></i><span class="menu-text">Principal</span></a> -->

    <a href="dashboard_admin.php" title="Dashboard"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
    <!-- <a href="chat_funcionario.php"><i class="bi bi-chat-left-text"></i><span class="menu-text">Mensagens</span></a> -->
    <a href="enviar_notificacao.php"><i class="bi bi-bell"></i><span class="menu-text">Notificações</span></a>
    <a href="admin_tickets.php"><i class="bi bi-ticket-perforated"></i><span class="menu-text">Tickets</span></a>
    <a href="gerar_voucher.php"><i class="bi bi-ticket-detailed"></i><span class="menu-text">Voucher</span></a>
    <a href="transacoes_admin.php"><i class="bi bi-currency-exchange"></i><span class="menu-text">Transações</span></a>
    <a href="saldo_clientes.php"><i class="bi bi-wallet2"></i><span class="menu-text">Saldo Clientes</span></a>
    <a href="movimentos_cliente.php"><i class="bi bi-bar-chart"></i><span class="menu-text">Movimentos</span></a>
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
    <h2 class="text-center mb-4">Pedidos dos Clientes</h2>
    <div class="table-status">
        <table class="table table-striked">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome do Cliente</th>
                    <th>Nome do Serviço</th>
                    <th>Categoria</th>
                    <th>Preço (Kz)</th>
                    <th>Estado</th>
                    <th>Data do Pedido</th>
                    <th>Data de Entrega</th>
                    <th>Progresso</th>
                    <th>Atendido por</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $estadoPedido = trim($row['estado']);
                    if ($estadoPedido == 'Pendente' || $estadoPedido == 'Cancelado') {
                        $percent = 0;
                    } elseif ($estadoPedido == 'Em andamento') {
                        $percent = 50;
                    } elseif ($estadoPedido == 'Concluído') {
                        $percent = 100;
                    } else {
                        $percent = 0;
                    }
                    if ($estadoPedido == 'Cancelado') {
                        $circleColor = '#ff0000';
                        $textColor   = '#ff0000';
                    } else {
                        $circleColor = '#00acc1';
                        $textColor   = '#666';
                    }
    
                    $dataEntrega = !empty($row['data_entrega']) 
                        ? date("Y-m-d", strtotime($row['data_entrega'])) 
                        : "";
                ?>
                <tr>
                    <td><?php echo $row['pedido_id']; ?></td>
                    <td><?php echo $row['cliente_nome']; ?></td>
                    <td><?php echo $row['servico_nome']; ?></td>
                    <td><?php echo $row['categoria_nome'] ? $row['categoria_nome'] : "Sem categoria"; ?></td>

                    <td><?php echo number_format($row['preco'], 2, ',', '.'); ?> Kz</td>
                   
                    <td>
                        <select class="estado-select" data-id="<?php echo $row['pedido_id']; ?>">
                            <option value="Pendente" <?php echo ($estadoPedido == 'Pendente' ? 'selected' : ''); ?>>Pendente</option>
                            <option value="Em andamento" <?php echo ($estadoPedido == 'Em andamento' ? 'selected' : ''); ?>>Em andamento</option>
                            <option value="Concluído" <?php echo ($estadoPedido == 'Concluído' ? 'selected' : ''); ?>>Concluído</option>
                            <option value="Cancelado" <?php echo ($estadoPedido == 'Cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                        </select>
                    </td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['data_pedido'])); ?></td>
                    <td>
                        <input type="date" class="data-entrega-input" data-id="<?php echo $row['pedido_id']; ?>" value="<?php echo $dataEntrega; ?>">
                    </td>
                    <td>
                        <div class="progress-circle" data-percentage="<?php echo $percent; ?>">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg"
                                      d="M18 2.0845
                                         a 15.9155 15.9155 0 0 1 0 31.831
                                         a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="circle"
                                      stroke-dasharray="<?php echo $percent; ?>, 100"
                                      stroke="<?php echo $circleColor; ?>"
                                      d="M18 2.0845
                                         a 15.9155 15.9155 0 0 1 0 31.831
                                         a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <text x="18" y="20.35" class="percentage" style="fill: <?php echo $textColor; ?>;">
                                    <?php echo $percent; ?>%
                                </text>
                            </svg>
                        </div>
                    </td>
                    <td>
                        <?php 
                        // Se o funcionário que atendeu o pedido estiver definido, exibe o nome; caso contrário, mostra "Não atendido"
                        echo !empty($row['funcionario_nome']) ? $row['funcionario_nome'] : "Não atendido"; 
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="cop">
    <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
</div>
<!-- Script para atualizar o estado via AJAX -->
<script>
    document.querySelectorAll('.estado-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var novoEstado = this.value;
            var pedidoId = this.getAttribute('data-id');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'updatePedidoEstado.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var progress = 0;
                        if(novoEstado === 'Pendente' || novoEstado === 'Cancelado') {
                            progress = 0;
                        } else if(novoEstado === 'Em andamento') {
                            progress = 50;
                        } else if(novoEstado === 'Concluído') {
                            progress = 100;
                        }
                        var row = select.parentNode.parentNode;
                        var progressCircle = row.querySelector('.progress-circle');
                        var circlePath = progressCircle.querySelector('.circle');
                        var percentageText = progressCircle.querySelector('.percentage');
                        
                        circlePath.setAttribute('stroke-dasharray', progress + ', 100');
                        
                        if(novoEstado === 'Cancelado'){
                            circlePath.setAttribute('stroke', '#ff0000');
                            percentageText.style.fill = '#ff0000';
                            select.style.color = 'red';
                        } else {
                            circlePath.setAttribute('stroke', '#00acc1');
                            percentageText.style.fill = '#666';
                            select.style.color = '';
                        }
                        
                        percentageText.textContent = progress + '%';
                    } else {
                        alert('Erro ao atualizar o estado!');
                    }
                }
            };
            xhr.send('pedido_id=' + pedidoId + '&estado=' + encodeURIComponent(novoEstado));
        });
    });
    
    // Atualiza a data de entrega via AJAX
    document.querySelectorAll('.data-entrega-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var novaData = this.value;
            var pedidoId = this.getAttribute('data-id');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'updatePedidoEntrega.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if(xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(!response.success) {
                        alert('Erro ao atualizar a data de entrega!');
                    }
                }
            };
            xhr.send('pedido_id=' + pedidoId + '&data_entrega=' + encodeURIComponent(novaData));
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
