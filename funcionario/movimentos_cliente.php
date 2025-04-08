<?php

require './veryFun.php';
require '../conexao.php';

if (!isset($_GET['id'])) {
    die("ID do cliente não informado.");
}

$id_cliente = intval($_GET['id']);
$filtro_tipo = isset($_GET['tipo']) ? $conexao->real_escape_string($_GET['tipo']) : '';
$filtro_data_inicio = isset($_GET['data_inicio']) ? $conexao->real_escape_string($_GET['data_inicio']) : '';
$filtro_data_fim = isset($_GET['data_fim']) ? $conexao->real_escape_string($_GET['data_fim']) : '';

// Recupera os dados do cliente
$stmt = $conexao->prepare("SELECT nome, sobrenome, saldo FROM Cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$stmt->bind_result($nome, $sobrenome, $saldo);
if (!$stmt->fetch()) {
    die("Cliente não encontrado.");
}
$stmt->close();

// Recupera os movimentos do cliente
$sqlTrans = "SELECT * FROM transacoes WHERE id_cliente = $id_cliente";
if ($filtro_tipo != '') {
    $sqlTrans .= " AND tipo = '$filtro_tipo'";
}
if ($filtro_data_inicio != '') {
    $sqlTrans .= " AND DATE(data) >= '$filtro_data_inicio'";
}
if ($filtro_data_fim != '') {
    $sqlTrans .= " AND DATE(data) <= '$filtro_data_fim'";
}
$sqlTrans .= " ORDER BY data DESC";
$resultado = $conexao->query($sqlTrans);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Movimentos de <?php echo htmlspecialchars($nome . " " . $sobrenome); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>\n        body { font-family: Arial, sans-serif; background-color: #f1f1f1; }\n    </style>
</head>
<body>
    <!-- Topbar e Sidebar mantêm a estrutura do sistema --> 
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
         <a href="admin.php"><span class="icon"><i class="bi bi-house"></i></span><span class="txt-link">Principal</span></a>
         <a href="saldo_clientes.php"><span class="txt-link">Saldo dos Clientes</span></a>
    </div>
    <div class="content">
        <div class="container mt-5">
            <h2>Movimentos de <?php echo htmlspecialchars($nome . " " . $sobrenome); ?></h2>
            <p>Saldo Atual: KZ <?php echo number_format($saldo, 2, ',', '.'); ?></p>
            <!-- Filtro de Movimentos --> 
            <form method="GET" class="mb-3">
                <input type="hidden" name="id" value="<?php echo $id_cliente; ?>">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo">
                    <option value="">Todos</option>
                    <option value="entrada" <?php if($filtro_tipo=='entrada') echo 'selected'; ?>>Entrada</option>
                    <option value="saida" <?php if($filtro_tipo=='saida') echo 'selected'; ?>>Saída</option>
                </select>
                <label for="data_inicio">De:</label>
                <input type="date" name="data_inicio" id="data_inicio" value="<?php echo htmlspecialchars($filtro_data_inicio); ?>">
                <label for="data_fim">Até:</label>
                <input type="date" name="data_fim" id="data_fim" value="<?php echo htmlspecialchars($filtro_data_fim); ?>">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="movimentos_cliente.php?id=<?php echo $id_cliente; ?>" class="btn btn-secondary">Limpar Filtros</a>
            </form>
            <!-- Links para Exportar --> 
            <div class="mb-3">
                <a href="exportar_pdf_admin.php?id=<?php echo $id_cliente; ?>&tipo=<?php echo urlencode($filtro_tipo); ?>&data_inicio=<?php echo urlencode($filtro_data_inicio); ?>&data_fim=<?php echo urlencode($filtro_data_fim); ?>" class="btn btn-danger">Exportar PDF</a>
                <a href="exportar_excel_admin.php?id=<?php echo $id_cliente; ?>&tipo=<?php echo urlencode($filtro_tipo); ?>&data_inicio=<?php echo urlencode($filtro_data_inicio); ?>&data_fim=<?php echo urlencode($filtro_data_fim); ?>" class="btn btn-success">Exportar Excel</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Valor (KZ)</th>
                        <th>Voucher</th>
                        <th>Referência</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($mov = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $mov['id']; ?></td>
                        <td><?php echo ucfirst($mov['tipo']); ?></td>
                        <td>KZ <?php echo number_format($mov['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo $mov['voucher'] ? $mov['voucher'] : '-'; ?></td>
                        <td><?php echo $mov['referencia_multicaixa'] ? $mov['referencia_multicaixa'] : '-'; ?></td>
                        <td><?php echo ucfirst($mov['status']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($mov['data'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="saldo_clientes.php" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
    <div class="cop">
         <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>
</body>
</html>
<?php $stmt->close(); ?>
