<?php
session_start();
require 'verifica.php';
require '../conexao.php';

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$mensagem = "";
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valor = floatval($_POST['valor']);
    if ($valor <= 0) {
        $erro = "Valor deve ser maior que zero.";
    } else {
        // Gera um código voucher e uma referência para Multicaixa
        $voucher = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $referencia = 'MC' . rand(100000, 999999);
        
        // Insere a transação com status "pendente"
        $stmt = $conexao->prepare("INSERT INTO transacoes (id_cliente, valor, tipo, voucher, referencia_multicaixa, status) VALUES (?, ?, 'carregamento', ?, ?, 'pendente')");
        $stmt->bind_param("idss", $id_cliente, $valor, $voucher, $referencia);
        if ($stmt->execute()) {
            $mensagem = "Transação criada com sucesso. Utilize a referência <strong>$referencia</strong> para efetuar o pagamento via Multicaixa.";
        } else {
            $erro = "Erro ao criar transação: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carregar Conta via Multicaixa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css">
  <style>
      body {
          background-color: #f8f9fa;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .container-custom {
          max-width: 500px;
          margin: 50px auto;
          background: #fff;
          padding: 30px;
          border-radius: 8px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      h2 {
          text-align: center;
          color: #343a40;
      }
      .btn-submit {
          width: 100%;
          padding: 15px;
          font-size: 18px;
      }

      .btn-back {
          width: 100%;
          padding: 15px;
          font-size: 18px;
          margin-top: 10px;
      }
  </style>
</head>
<body>
  <div class="container">
      <div class="container-custom">
          <h2>Carregar Conta via Multicaixa</h2>
          <?php if (!empty($erro)): ?>
              <div class="alert alert-danger" role="alert">
                  <?php echo $erro; ?>
              </div>
          <?php endif; ?>
          <?php if (!empty($mensagem)): ?>
              <div class="alert alert-success" role="alert">
                  <?php echo $mensagem; ?>
              </div>
          <?php endif; ?>
          <p class="text-center">Insira o valor em Kz que deseja carregar na sua conta.</p>
          <form method="POST" action="">
              <div class="mb-3">
                  <label for="valor" class="form-label">Valor (Kz):</label>
                  <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
              </div>
              <button type="submit" class="btn btn-primary btn-submit">
                  <i class="bi bi-cash-stack"></i> Gerar Referência de Pagamento
              </button>          
          </form>
          <a href="carregamento.php" class="btn btn-secondary btn-back">
              <i class="bi bi-arrow-left"></i> Voltar ao Painel de Carregamento
          </a>
      </div>
      
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
