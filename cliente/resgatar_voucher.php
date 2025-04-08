<?php
session_start();
require 'verifica.php';
require '../conexao.php';

if (!isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$erro = "";
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucher_code = trim($_POST['voucher_code']);
    if (empty($voucher_code)) {
        $erro = "Por favor, informe o código do voucher.";
    } else {
        // Procura o voucher no banco de dados
        $stmt = $conexao->prepare("SELECT id, valor, used FROM vouchers WHERE voucher_code = ?");
        $stmt->bind_param("s", $voucher_code);
        $stmt->execute();
        $stmt->bind_result($voucher_id, $valor, $used);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($used) {
                $erro = "Voucher já utilizado.";
            } else {
                // Marcar voucher como usado e registrar quem o usou
                $stmt = $conexao->prepare("UPDATE vouchers SET used = 1, used_by = ?, used_date = NOW() WHERE id = ?");
                $stmt->bind_param("ii", $id_cliente, $voucher_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    // Atualizar o saldo do cliente
                    $stmt = $conexao->prepare("UPDATE Cliente SET saldo = saldo + ? WHERE id_cliente = ?");
                    $stmt->bind_param("di", $valor, $id_cliente);
                    if ($stmt->execute()) {
                        $mensagem = "Voucher resgatado com sucesso! Seu saldo foi atualizado em KZ " . number_format($valor, 2, ',', '.');
                    } else {
                        $erro = "Erro ao atualizar o saldo do cliente.";
                    }
                    $stmt->close();
                } else {
                    $erro = "Erro ao atualizar o voucher.";
                }
            }
        } else {
            $erro = "Voucher não encontrado.";
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Resgatar Voucher</title>
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
          margin-bottom: 20px;
      }
      .btn-submit {
          width: 100%;
          padding: 15px;
          font-size: 18px;
          margin-top: 20px;
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
          <h2>Resgatar Voucher</h2>
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
          <form method="POST" action="">
              <div class="mb-3">
                  <label for="voucher_code" class="form-label">Código do Voucher:</label>
                  <input type="text" class="form-control" id="voucher_code" name="voucher_code" required>
              </div>
              <button type="submit" class="btn btn-primary btn-submit">
                  <i class="bi bi-ticket-detailed"></i> Resgatar Voucher
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
