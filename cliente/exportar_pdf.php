<?php
ob_start();
session_start();
require 'verifica.php';
require '../conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Parâmetros de filtro
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

$sql = "SELECT * FROM transacoes WHERE id_cliente = $id_cliente";
if ($filtro_tipo != '') {
    $sql .= " AND tipo = '" . $conexao->real_escape_string($filtro_tipo) . "'";
}
if ($filtro_data_inicio != '') {
    $sql .= " AND DATE(data) >= '" . $conexao->real_escape_string($filtro_data_inicio) . "'";
}
if ($filtro_data_fim != '') {
    $sql .= " AND DATE(data) <= '" . $conexao->real_escape_string($filtro_data_fim) . "'";
}
$sql .= " ORDER BY data DESC";
$result = $conexao->query($sql);

// Inclui o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Definindo o caminho absoluto para o logo usando file://
$logoPath = __DIR__ . '/../assets/logo.png';
if(file_exists($logoPath)){
    $logoPath = 'file://'.$logoPath;
} else {
    $logoPath = '';
}

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GeovaneServices');
$pdf->SetTitle('Movimentos - Cliente');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Cabeçalho personalizado com logo e informações da empresa
$companyHeader = '
    <table border="0" cellpadding="5">
      <tr>
        <td width="20%">' . ($logoPath ? '<img src="' . $logoPath . '" width="80">' : '') . '</td>
        <td width="80%" style="text-align: center;">
          <h1 style="margin:0; padding:0;">GeovaneServices</h1>
          <p style="margin:0; font-size:16px;">Sua Solução em Serviços</p>
        </td>
      </tr>
    </table>
    <hr>';
$pdf->writeHTML($companyHeader, true, false, true, false, '');

// Monta a tabela com os dados dos movimentos
$html = '<h2 style="text-align: center;">Movimentos</h2>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr style="background-color: #f0f0f0;">
            <th>ID</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Voucher</th>
            <th>Referência</th>
            <th>Status</th>
            <th>Data</th>
          </tr>';
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row['id'] . '</td>';
    $html .= '<td>' . ucfirst($row['tipo']) . '</td>';
    $html .= '<td>' . number_format($row['valor'], 2, ',', '.') . '</td>';
    $html .= '<td>' . ($row['voucher'] ? $row['voucher'] : '-') . '</td>';
    $html .= '<td>' . ($row['referencia_multicaixa'] ? $row['referencia_multicaixa'] : '-') . '</td>';
    $html .= '<td>' . ucfirst($row['status']) . '</td>';
    $html .= '<td>' . date('d/m/Y H:i', strtotime($row['data'])) . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Limpa o buffer e envia o PDF
ob_end_clean();
$pdf->Output('movimentos_cliente.pdf', 'I');
?>
