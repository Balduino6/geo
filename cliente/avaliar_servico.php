<?php
session_start();
require 'verifica.php';
require 'conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

// O id do pedido a ser avaliado deve ser passado via GET
if (!isset($_GET['pedido']) || empty($_GET['pedido'])) {
    die("ID do pedido não informado.");
}

$id_pedido = intval($_GET['pedido']);
$id_cliente = $_SESSION['id_cliente'];

// Verifica se o pedido pertence ao cliente e se está concluído (você pode ajustar conforme sua lógica)
$sql = "SELECT p.id, s.nome AS servico_nome, p.estado FROM Pedidos p INNER JOIN Servicos s ON p.id_servico = s.id_servico WHERE p.id = ? AND p.id_cliente = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Pedido não encontrado ou não pertence a você.");
}
$pedido = $result->fetch_assoc();
$stmt->close();

// Se o pedido não estiver concluído, não permite avaliação
if ($pedido['estado'] !== 'Concluído') {
    die("A avaliação só pode ser feita para pedidos concluídos.");
}

$mensagemSucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avaliacao = intval($_POST['avaliacao']);
    $comentario = trim($_POST['comentario']);

    if ($avaliacao < 1 || $avaliacao > 5) {
        $erro = "A avaliação deve ser entre 1 e 5.";
    } elseif (empty($comentario)) {
        $erro = "Por favor, insira um comentário.";
    } else {
        // Insere a avaliação
        $sqlInsert = "INSERT INTO avaliacoes (id_cliente, id_pedido, avaliacao, comentario) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conexao->prepare($sqlInsert);
        $stmtInsert->bind_param("iiis", $id_cliente, $id_pedido, $avaliacao, $comentario);
        if ($stmtInsert->execute()) {
            $mensagemSucesso = "Avaliação enviada com sucesso!";
        } else {
            $erro = "Erro ao enviar avaliação: " . $conexao->error;
        }
        $stmtInsert->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Avaliar Serviço - Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1f1f1; margin: 0; }
        .topbar, .sidebar, .cop { /* Utilize sua estrutura padrão */ }
        .content { margin-left: 250px; margin-top: 120px; padding: 20px; }
        .card-avaliacao { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <!-- Topbar (copie a estrutura do seu sistema) -->
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
    <!-- Sidebar (copie sua estrutura) -->
    <div class="sidebar">
         <a href="cliente.php"><i class="bi bi-house"></i> Principal</a>
         <a href="pedido_servico.php"><i class="bi bi-cart"></i> Pedir Serviço</a>
         <a href="meu_saldo.php"><i class="bi bi-cash-stack"></i> Meu Saldo</a>
         <a href="perfil.php"><i class="bi bi-person"></i> Perfil</a>
         <a href="notificacoes.php"><i class="bi bi-bell"></i> Notificações</a>
         <a href="configuracao.php"><i class="bi bi-gear-wide-connected"></i> Configurações</a>
         <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
    <!-- Conteúdo Principal -->
    <div class="content">
         <div class="card-avaliacao">
             <h2>Avaliar Serviço</h2>
             <p>Serviço: <strong><?php echo htmlspecialchars($pedido['servico_nome']); ?></strong></p>
             <?php if (!empty($mensagemSucesso)): ?>
                 <div class="alert alert-success"><?php echo $mensagemSucesso; ?></div>
             <?php endif; ?>
             <?php if (!empty($erro)): ?>
                 <div class="alert alert-danger"><?php echo $erro; ?></div>
             <?php endif; ?>
             <form action="avaliar_servico.php?pedido=<?php echo $id_pedido; ?>" method="post">
                 <div class="mb-3">
                     <label for="avaliacao" class="form-label">Avaliação (1 a 5):</label>
                     <input type="number" name="avaliacao" id="avaliacao" class="form-control" min="1" max="5" required>
                 </div>
                 <div class="mb-3">
                     <label for="comentario" class="form-label">Comentário:</label>
                     <textarea name="comentario" id="comentario" class="form-control" rows="4" required></textarea>
                 </div>
                 <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
                 <a href="meus_servicos.php" class="btn btn-secondary">Cancelar</a>
             </form>
         </div>
    </div>
    <!-- Rodapé (copie sua estrutura) -->
    <div class="cop">
         <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>
</body>
</html>
