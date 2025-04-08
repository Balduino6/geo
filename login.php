<?php 
include_once 'conexao.php';

  $msg = $_GET['msg'] ?? '';

    if ($msg == 'credenciais_incorretas') {
        $mensagem = "Credenciais incorretas. Por favor, tente novamente.";
    } elseif ($msg == 'tipo_acesso_invalido') {
        $mensagem = "Tipo de acesso inválido. Por favor, escolha entre Funcionário e Administrador.";
    } else {
        $mensagem = "";
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema Geovane Services</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Estilos gerais */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    background-color: #f1f1f1;
}

.topbar {
    width: 100%;
    background-color: #333;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    z-index: 1000;
  }

  .topbar .logo img {
    width: 150px;
    border-radius: 5%;
}


.menu-icon {
    display: none;
    font-size: 30px;
    color: white;
    cursor: pointer;
    margin-right: 10px;
}

.menu {
    display: flex;
    gap: 20px;
    margin-left: auto;
    
}

.menu a {
    color: whitesmoke;
    text-decoration: none;
    /* font-size: 18px; */
    /* padding: 8px 12px; */
    border-radius: 5px;

    font-size: 16px; /* Diminuindo um pouco o tamanho da fonte */
    padding: 6px 10px; /* Ajustando o espaçamento */
}

.menu a:hover {
    color: #ddd;
    background-color: #444;
}

    .content {
      margin-top: 120px; /* Espaço para a topbar */
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: white;
      padding: 30px;
      border-radius: 5px;
      box-shadow: 1px 1px 5px #ccc;
      width: 460px;
    }
    .card h2 {
      text-align: center;
      color: #180F4A;
      margin-bottom: 20px;
    }
    .card-group {
      margin-bottom: 10px;
    }
    .card-group label {
      display: block;
      margin-bottom: 5px;
      color: #4b4b4b;
    }
    .card-group input,
    .card-group select {
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
    }
    #acessar {
      width: 70%;
      margin: 20px auto 0;
      padding: 15px;
      border: none;
      border-radius: 30px;
      background-image: linear-gradient(to right, #555, #444);
      color: white;
      cursor: pointer;
      display: block;
    }
    #acessar:hover {
      background-image: linear-gradient(to right, #444, #555);
    }
    .conta {
      text-align: center;
      margin-top: 15px;
      font-size: 15px;
      color: #180F4A;
      text-decoration: none;
    }
    .alert { margin-top: 10px; }

    footer {
      background-color: #333;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: auto;
    }

    @media (max-width: 768px) {
    .menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 80px;
        right: 0;
        background-color: #333;
        width: 50%;
        text-align: center;
        border-bottom-left-radius:5%;
       
    }

    .menu a {
        padding: 15px 0;
        font-size: 20px;
        transition:.3s;
        border-bottom-left-radius:5%;
    }

    .menu.active {
        display: flex;
    }

    .menu-icon {
        display: block;
    }
}
  </style>
</head>
<body>
  <!-- Topbar -->
  <div class="topbar">
      <div class="logo">
          <img src="assets/logo.png" alt="Logo">
      </div>
      
      <div class="menu-icon" onclick="toggleMenu()">
        <i id="menuIcon" class="bi bi-list"></i>
    </div>
      
    <div class="menu">
        <a href="../index.php">Principal</a>
        <a href="../sobre.php">Sobre Nós</a>
        <a href="#">Serviços</a>
        <a href="./registroCliente.php">Regista-se</a>
        <a href="./login.php">Login</a>
    </div>

  </div>
 
  <script>
    function toggleMenu() {
        document.querySelector('.menu').classList.toggle('active');
    }
</script>
  
  <!-- Conteúdo -->
  <div class="content">
      <form class="card" action="acesso.php" method="post">
          <h2>Faça o seu login</h2>
          <div class="card-group">
              <label>Usuário</label>
              <input type="text" name="usuario" placeholder="Nome de Usuário" required>
          </div>
          <div class="card-group">
              <label>Senha</label>
              <input type="password" name="senha" placeholder="Digite sua senha" required>
          </div>
          <div class="card-group">
              <label for="acesso">Acesso</label>
              <select name="acesso" id="acesso" required>
                  <option selected disabled value="">Tipo de acesso</option>
                  <option value="funcionario">Funcionário</option>
                  <option value="administrador">Administrador</option>
              </select>
          </div>
          
          <?php if (!empty($mensagem)): ?>
              <div class="alert alert-danger"><?php echo $mensagem; ?></div>
          <?php endif; ?>
          <input type="submit" name="submit" id="acessar" value="Entrar">
          <a class="conta" href="cliente/login.php">Cliente</a>
      </form>
  </div>
  

  <footer>
      <p>© Todos direitos reservados por GeovaneServices</p>
  </footer>
</body>
</html>
