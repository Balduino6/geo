<?php
session_start();
require 'verifica.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>FAQ - Geovane Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; background-color: #f1f1f1; margin: 0; }
    .topbar { /* Copie os estilos da sua topbar */ }
    .sidebar { /* Copie os estilos da sua sidebar */ }
    .content { margin-left: 250px; margin-top: 120px; padding: 20px; }
    .cop { text-align: center; padding: 20px; border-top: 1px solid #ddd; color: #444; }
    .faq-item { margin-bottom: 20px; }
    .faq-question { font-weight: bold; }
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
      <a href="faq.php" style="color: white;"><i class="bi bi-question-circle"></i> FAQ</a>
      <a href="configuracao.php"><i class="bi bi-gear-wide-connected"></i> Configurações</a>
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
  </div>
  <!-- Conteúdo Principal -->
  <div class="content">
      <div class="container">
          <h2>Perguntas Frequentes (FAQ)</h2>
          <div class="faq-item">
              <p class="faq-question">1. Como faço para solicitar um serviço?</p>
              <p>Você pode solicitar um serviço clicando em “Pedir Serviço” no menu e escolhendo a opção desejada.</p>
          </div>
          <div class="faq-item">
              <p class="faq-question">2. Como posso carregar minha conta?</p>
              <p>Você pode carregar sua conta acessando a opção “Carregar Conta” no menu. Escolha o método de pagamento (Multicaixa ou Voucher) e siga as instruções.</p>
          </div>
          <div class="faq-item">
              <p class="faq-question">3. Como acompanho meus pedidos?</p>
              <p>Você pode acompanhar seus pedidos na área “Meus Serviços” ou “Meu Saldo”, onde também são exibidos os históricos financeiros.</p>
          </div>
          <div class="faq-item">
              <p class="faq-question">4. Como posso entrar em contato com o suporte?</p>
              <p>Utilize a Central de Suporte para enviar um ticket ou entre em contato através do chat.</p>
          </div>
      </div>
  </div>
  <!-- Rodapé -->
  <div class="cop">
      <p>© Todos direitos reservados por GeovaneServices</p>
  </div>
</body>
</html>
