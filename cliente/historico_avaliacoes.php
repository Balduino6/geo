<?php
session_start();
require 'verifica.php';
require 'conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Consulta as avaliações do cliente, unindo com o nome do serviço
$sql = "SELECT a.avaliacao, a.comentario, a.data, s.nome AS servico_nome 
        FROM avaliacoes a 
        INNER JOIN Pedidos p ON a.id_pedido = p.id 
        INNER JOIN Servicos s ON p.id_servico = s.id_servico 
        WHERE a.id_cliente = $id_cliente 
        ORDER BY a.data DESC";
$result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Avaliações - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; margin: 0; }
        .topbar, .sidebar, .cop { /* Utilize sua estrutura */ }
        .content { margin-left: 250px; margin-top: 120px; padding: 20px; }
    </style>
</head>
<body>
    <!-- Topbar (mesma estrutura do sistema) -->
    <div class="topbar">
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo" style="width:185px; border-radius:5px;">
        </div>
        <div class="search-box">
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Pesquisar no sistema...">
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="user-info">
            <img src="../upload/imagens_perfil/<?php echo isset($_SESSION['imagemPerfil']) ? $_SESSION['imagemPerfil'] : 'default-avatar.png'; ?>" alt="Foto de Perfil" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
            <div class="username">Olá, <?php echo $_SESSION['nomeUser'] . " " . $_SESSION['sobrenome']; ?></div>
            <a href="logout.php" style="color: white; margin-left: 10px;">Sair <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
    <!-- Sidebar (mesma estrutura do sistema) -->
    <div class="sidebar">
         <a href="cliente.php"><i class="bi bi-house"></i> Principal</a>
         <a href="pedido_servico.php"><i class="bi bi-cart"></i> Pedir Serviço</a>
         <a href="meu_saldo.php"><i class="bi bi-cash-stack"></i> Meu Saldo</a>
         <a href="perfil.php"><i class="bi bi-person"></i> Perfil</a>
         <a href="notificacoes.php"><i class="bi bi-bell"></i> Notificações</a>
         <a href="historico_avaliacoes.php" style="color: white;"><i class="bi bi-star"></i> Avaliações</a>
         <a href="configuracao.php"><i class="bi bi-gear-wide-connected"></i> Configurações</a>
         <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
    <!-- Conteúdo Principal -->
    <div class="content">
         <div class="container">
             <h2>Histórico de Avaliações</h2>
             <?php if ($result->num_rows > 0): ?>
             <table class="table table-striped">
                 <thead>
                     <tr>
                         <th>Serviço</th>
                         <th>Avaliação</th>
                         <th>Comentário</th>
                         <th>Data</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php while ($avaliacao = $result->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo htmlspecialchars($avaliacao['servico_nome']); ?></td>
                         <td><?php echo $avaliacao['avaliacao']; ?>/5</td>
                         <td><?php echo htmlspecialchars($avaliacao['comentario']); ?></td>
                         <td><?php echo date("d/m/Y H:i", strtotime($avaliacao['data'])); ?></td>
                     </tr>
                     <?php endwhile; ?>
                 </tbody>
             </table>
             <?php else: ?>
                 <p>Você ainda não avaliou nenhum serviço.</p>
             <?php endif; ?>
         </div>
    </div>
    <!-- Rodapé --> 
    <div class="cop">
         <p class="copy">© Todos direitos reservados por GeovaneServices</p>
    </div>
</body>
</html>
