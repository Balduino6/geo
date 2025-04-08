<?php
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login Funcionário</title>
    <!-- <link rel="stylesheet" href="./css/admLog.css"> -->
    <?php require'loginFunc.php';?>
</head>
 <body>
    
    <nav class="navbar">
        <div class="logo">
           <img src="./assets/logo.png">
        </div>

        <div class="menu">
            <a href="index.php">Página Principal</a>
            <a href="servicos.php">Serviços</a>
            <a href="#">Sobre Nós</a>
            <!-- <a href="registro.php">Registrar-se</a> -->
            <!-- <a href="login.php">Login</a> -->
            
            <!-- <a id="sair" href="logout.php">Sair</a> -->
        </div>

    </nav>

    <header class="headerline">  

        <form class="form" action="acesso.php" method="post">
            <div class="card">

                <div class="card-top">
                    <!-- <img class="imgLogin" src="./assets/user.jpg" alt=""> -->
                    <h2 class="title">Faça o seu login</h2>
                    <!-- <p>Gerenciar seu negócio</p> -->
                </div>

                <div class="card-group">
                    <label for="">Usuário</label>
                    <input type="text" name="usuario" placeholder="Nome de Usuário" required>
                </div>

                <div class="card-group">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="Digite uma senha" required>
                </div>

                <div class="card-group">
                    <label for="acesso">Acesso</label>
                    <select name="acesso" id="acesso" required >
                        <option selected disabled="" value="Selecione o tipo de acesso" >Tipo de acesso</option>
            
                        <option value="funcionario" name="acesso">Funcionário</option>
            
                        <option value="administrador" name="acesso">Administrador</option>
                    </select>

                </div> 
                <br>
                <!-- <div class="card-group">
                    <label><input type="checkbox" >Lembre-me</label>
                </div> -->
                
                <?php echo $mensagem; ?>
                <br><br>
                
                <input type="submit" name="submit" id="acessar" value="Entrar">
                <br><br>
                <a class="conta" href="loginCli.php">Cliente</a>
                
                <!-- <a class="conta" href="registro.php">Não tem uma conta? Regista-se!</a> -->
    
            </div>

        </form>
    </header>
 
    <footer>      
        <!--<div class="logo-rodape"> 
        <h1 class="agencia" >Agência Fake</h1>
        <p style="color: white;">Feito com HTML e CSS puro</p>
        </div>
        <b style="color: white;">© Todos os direitos reservados.</b> -->

        <div class="contact">
            <h2>Contactos</h2>
            <p>GEOVANE SERVICES</p>
            <p>Futungo de Belas, Luanda</p>
        </div>

        <div class="tel">
            <p>Tel: 933416260</p>
            <p>Email: geovaneservices@gmail.com</p> 
            <a class="social-links-a" href="">
                <img class="social-links" src="./assets/face.png">
            </a>

            <a href="" class="social-links-a">
                <img  class="social-links" src="./assets/insta.png">   
            </a>

            <a class="social-links-a" href="https://youtube.com/maykbrito">
                <img class="social-links" src="./assets/you.png">
            </a>
        </div>

        <div class="qr">
            <img src="./assets/qr.png" alt="">
        </div>
    </footer>

    <div class="cop">
        <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>

    <!-- <a class="whats" href="https://wa.link/cwu58y" target="_blank">
        <img src="./assets/whats.png" alt="">
    </a> -->
       

</body>
</html>