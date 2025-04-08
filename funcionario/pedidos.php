<?php
require './veryFun.php';
require '../conexao.php';

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

// Consulta os pedidos dos clientes com os dados do serviço, do cliente e a data de entrega
$query = "
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
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .topbar .logo img {
            width: 185px;
            border-radius: 5px;
        }
        .search-box {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            margin: 0 20px;
        }
        .search-box form {
            display: flex;
            width: 50%;
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
        .search-box button:hover {
            background-color: #575757;
            border-color: #444;
        }
        .user-info {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        .user-info .username {
            margin-right: 15px;
            font-size: 18px;
            color: white;
        }
        .user-info a {
            color: whitesmoke;
            text-decoration: none;
            font-size: 25px;
            padding: 1px 3px;
            border-radius: 5%;
            margin-right: 30px;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }
        .user-info a:hover {
            color: #ddd;
            transition: 0.1s;
        }
        .perfil-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }
        .sidebar {
            width: 250px;
            background-color: #444;
            color: white;
            padding-top: 80px;
            position: fixed;
            height: 100%;
            top: 0;
            padding-left: 20px;
            overflow-y: auto;
        }
        .sidebar a {
            margin-top: 50px;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            font-size: 20px;
            padding: 20px 4%;
            transition: 0.3s;
            margin-bottom: 5px;
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
            padding: 20px;
            flex-grow: 1;
            overflow-y: auto;
            margin-top: 120px;
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
        <a href="admin.php"> <span class="icon"><i class="bi bi-house"></i></span>
        <span class="txt-link">Principal</span></a>

        <a href="dashboard_admin.php"><img src="../assets/dashboard.png" alt="" class="icon" style="width: 30px;">Dasboard</a>

        <a href="chat_funcionario.php"><img src="../assets/chat.png" alt="" class="icon" style="width: 30px;">Mensagens</a>
        
        <a href="enviar_notificacao.php"><img src="../assets/notification.png" alt="" class="icon" style="width: 30px;">Enviar Notificacoes</a>

        <a href="ver_notificacoes.php"><img src="../assets/notifications.png" alt="" class="icon" style="width: 30px;">Ver Notificações</a>

        <a href="admin_tickets.php"><img src="../assets/tickets.png" alt="" class="icon" style="width: 30px;">Tickets</a>
    
        <a href="gerar_voucher.php"><img src="../assets/voucher.png" alt="" class="icon" style="width: 30px;">Gerar Voucher</a>

        <a href="transacoes_admin.php"><img src="../assets/transacoes.png" alt="" class="icon" style="width: 30px;">Transações</a>

        <a href="saldo_clientes.php"><img src="../assets/saldo.png" alt="" class="icon" style="width: 30px;">Saldo dos Clientes</a>

        <a href="movimentos_cliente.php?"><img src="../assets/debit.png" alt="" class="icon" style="width: 30px;">Movimentos de um Cliente</a>
    
        <a href="exibirServicos.php"><img src="../assets/services.png" alt="" class="icon" style="width: 30px;">Ver Serviços</a>

        <a href="cadastrarServico.php"><img src="../assets/registration.png" alt="" class="icon" style="width: 30px; color:#f5f5f5;">Registrar Serviços</a>

        <a href="registroFun.php"><img src="../assets/regFun.png" alt="" class="icon" style="width: 30px;">Registrar Funcionário</a>

        <a href="registroCliente.php">
        <img src="../assets/regCli.png" alt="" class="icon" style="width: 30px;">Registrar  Cliente</a>

        <a href="controlFunci.php"><img src="../assets/employee.png" alt="" class="icon" style="width: 30px;">Controle de Funcionário</a>

        <a href="controlCliente.php"><img src="../assets/customer.png" alt="" class="icon" style="width: 30px;">Controle de Clientes</a>

        <a href="adm_pedido.php"><img src="../assets/clienteP.png" alt="" class="icon" style="width: 30px;">Pedidos de Serviços</a>

        <a href="perfil.php" style="color: white;"><img src="../assets/service.png" alt="" class="icon" style="width: 30px;">Configurações</a>

        <a href="relatorios.php"><img src="../assets/relatorio.png" alt="" class="icon" style="width: 30px;">Relatorios</a>

        <a href="./logout.php"><img src="../assets/sair.png" alt="" class="icon" style="width: 30px;">Sair</a>
        
    </div>

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
                                <!-- Campo para definir a data de entrega -->
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
                                    
                                 echo   $row['funcionario_nome'];
                                    
                                ?>
                            </td> 
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
        
        // Script para atualizar a data de entrega via AJAX
        document.querySelectorAll('.data-entrega-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var novaData = this.value; // Formato YYYY-MM-DD
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
</body>
</html>
