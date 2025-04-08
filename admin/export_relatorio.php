<?php

require './verifyadm.php'; // Verifica se o administrador está logado
require '../conexao.php';

// Obtém os filtros de data, se definidos
$filter = "";
if (isset($_GET['start_date'], $_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $start_date = $conexao->real_escape_string($_GET['start_date']);
    $end_date   = $conexao->real_escape_string($_GET['end_date']);
    $filter = " WHERE p.data_pedido BETWEEN '$start_date' AND '$end_date' ";
}

// Consulta para exportar os dados (ajuste conforme sua estrutura)
$query = "
    SELECT 
        p.id AS pedido_id, 
        p.data_pedido, 
        p.data_entrega, 
        p.estado, 
        c.nome AS cliente_nome, 
        s.nome AS servico_nome, 
        s.preco AS servico_preco
    FROM Pedidos p
    INNER JOIN Cliente c ON p.id_cliente = c.id_cliente
    INNER JOIN Servicos s ON p.id_servico = s.id_servico
    $filter
    ORDER BY p.data_pedido DESC
";
$result = $conexao->query($query);
if (!$result) {
    die("Erro na query: " . $conexao->error);
}

// Define os headers para download em CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_export_' . date("Ymd_His") . '.csv');

// Abre o stream de saída
$output = fopen('php://output', 'w');

// Escreve os cabeçalhos das colunas
fputcsv($output, ['Pedido ID', 'Data do Pedido', 'Data de Entrega', 'Estado', 'Cliente', 'Serviço', 'Preço']);

// Loop para escrever cada linha do CSV
while ($row = $result->fetch_assoc()) {
    // Formata as datas, se existirem
    $row['data_pedido'] = date("d/m/Y H:i", strtotime($row['data_pedido']));
    $row['data_entrega'] = $row['data_entrega'] ? date("d/m/Y H:i", strtotime($row['data_entrega'])) : '';
    fputcsv($output, $row);
}
fclose($output);
exit;
