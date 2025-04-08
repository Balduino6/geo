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
    $imagem = $_FILES['imagem'];
    $nomeArquivo = time() . '_' . $imagem['name'];
    $destino = '../upload/imagens_perfil/' . $nomeArquivo;
    if (move_uploaded_file($imagem['tmp_name'], $destino)) {
        $imagemPerfil = $nomeArquivo;
    } else {
        $erro .= "Erro ao mover o arquivo de imagem. ";
        $imagemPerfil = $funcionario['imagem_perfil'];
    }
} else {
    $imagemPerfil = $funcionario['imagem_perfil'];
}

$query = "SELECT t.*, c.nome AS cliente_nome FROM transacoes t INNER JOIN Cliente c ON t.id_cliente = c.id_cliente ORDER BY t.data DESC";
$result = $conexao->query($query);

if (isset($_GET['confirmar']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Obter a transação
    $stmt = $conexao->prepare("SELECT * FROM transacoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $transacao = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($transacao && $transacao['status'] == 'pendente' && $transacao['tipo'] == 'carregamento') {
        // Atualizar a transação para confirmado
        $stmt = $conexao->prepare("UPDATE transacoes SET status = 'confirmado' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Atualizar o saldo do cliente
        $stmt = $conexao->prepare("UPDATE Cliente SET saldo = saldo + ? WHERE id_cliente = ?");
        $stmt->bind_param("di", $transacao['valor'], $transacao['id_cliente']);
        $stmt->execute();
        $stmt->close();

        header("Location: transacoes_admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Transações</title>
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
            width: 150px;
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
            top: 120px;
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
            top: 130px;
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
        .table-status {
            background: white;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
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
        <a href="dashboard_admin.php" title="Dashboard"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
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
    <!-- Conteúdo -->
    <div class="content">
        <div class="table-status">
            <h2>Transações</h2>
            <table class="table table-stricked">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor(Kz)</th>
                        <th>Tipo</th>
                        <th>Voucher</th>
                        <th>Referência</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['cliente_nome']; ?></td>
                        <td><?php echo $row['valor']; ?></td>
                        <td><?php echo $row['tipo']; ?></td>
                        <td><?php echo $row['voucher']; ?></td>
                        <td><?php echo $row['referencia_multicaixa']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td>
                            <?php if ($row['tipo'] == 'carregamento' && $row['status'] == 'confirmado'): ?>
                                <a href="receipt.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" target="_blank">Recibo</a>
                            <?php elseif ($row['tipo'] == 'carregamento' && $row['status'] == 'pendente'): ?>
                                <a href="transacoes_admin.php?confirmar=1&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Confirmar</a>
                            <?php endif; ?>
                        </td>
                    
                
                    </tr>
                    <?php endwhile; ?>
                    
                </tbody>
            </table>
           
        </div>
        <a href="lista_recibos.php" class="btn btn-primary btn-sm">Ver Recibos</a>
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
        
        const links = document.querySelectorAll('.sidebar a');
        const currentPage = window.location.pathname.split('/').pop();

        links.forEach(link => {
            const linkPage = link.getAttribute('href').split('/').pop();
            if (linkPage === currentPage) {
                link.classList.add('active');
            }
        });

        // Inicializa tooltips do Bootstrap, se necessário
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
