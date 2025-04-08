<?php 
    require 'verifica.php';
    if(isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario'])):?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços</title>
    <!-- <link rel="stylesheet" href="css/servicos.css"> -->

    <?php require 'estilo.php'; ?>

</head>
<body>
    <nav class="navbar">
        <div class="logo">
           <img src="./assets/logo.png">
        </div>

        <div class="menu">
            <label style="font-size: 20px; color: #2209af; "><b>Usuário:</b><?php  echo  " $nomeUser ". "$sobrenome"; ?></label>
            <a href="logged.php">Página Principal</a>
            <a href="#">Sobre Nós</a>
            <a href="servicos.php">Serviços</a>
           
            <!-- <a href="registro.html">Registrar-se</a> -->
            <!-- <a href="login.html">Login</a> -->

            <!-- <a id="botao" href="#">Fale Conosco</a> -->
            <!-- <a href="logout.php">Sair</a> -->
        </div>

        <div class="form-inline">
            <!-- <?php echo $nomeUser.$sobrenome;  ?> -->
            <img src="./assets/sair.png" alt="" style="width: 20px;">
            <a href="logout.php">Sair</a>
        </div>

    </nav>  

    <header class="header" style="background-image: url(./assets/bodyimg.jpg); height: 500px;" >
             
             <div class="headline">
     
                 <h2 style="letter-spacing: 15px;">Bem-Vindo à GEOVANE<br>SERVICES</h2>
                 <p class="ph2">Funcionalidade é a nossa função</p>
     
                 <!--<a href="#" class="contact-btn">Contrate Agora</a>-->
             </div>
         </header>
     
     
         <section>
             <h2>Serviços</h2>
             <div class="servicos">
                 <div class="card">
                     <img src="./assets/pc.png">
                     <div class="card-text">
                         <h3>Desenvolvimento de Sites</h3>
                         <p>Desenvolvemos sites simples e complexos, portifólios e muito mais.</p>
                     </div>
                                    
                 </div>
     
                 <div class="card">
                     <img src="./assets/computer.png">
                     <div class="card-text">
                         <h3>Montagem de Redes LANS</h3>
                         <p>Montagem de redes locais virtuais cliente servidor com cabeamento extruturado.</p>
                     </div>
     
                 </div>
     
                 <div class="card">
                     <img src="./assets/web.png">
                     <div class="card-text">
                     <h3>Manutenção de infraestrutura de redes</h3>
                     <p>Manutenção na infaestrutura de rede, </p>
                     </div>  
     
                 </div>
     
                 <div class="card">
                     <img src="./assets/shooting.png">
                     <div class="card-text">
                     <h3>Manutenção de Computadores</h3>
                     <p></p>
                     </div>  
     
                 </div>
     
                 <div class="card">
                     <img src="./assets/project.png">
                     <div class="card-text">
                     <h3>Venda de Projectos</h3>
                     <p></p>
                     </div>  
     
                 </div>
     
                 <div class="card">
                     <img src="./assets/printer.png">
                     <div class="card-text">
                     <h3>Cyber Coffee</h3>
                     <p></p>
                     </div>  
     
                 </div>
     
             </div>
     
         </section> 
         
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

         <a class="whats" href="https://wa.link/cwu58y" target="_blank">
        <img src="./assets/whats.png" alt="">
        </a>
    
</body>
</html>

<?php else:header('Location: login.php'); endif; ?>