<?php 
    session_start();
    require 'verifica.php';
    if(isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente'])):?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<style>
     button{
            background-image: linear-gradient(to right,  #555, #444);
        }

    button:hover{
        background-image: linear-gradient(to right, #444 , #555);

        outline: none;
    }

    .btn{
        color: white;
    }

    .btn:hover{
        color: white;
    }   
</style>

<div class="container mt-5">
    <div class="alert alert-success" role="alert">
        Pedido feito com sucesso!
    </div>
    <a href="pedido_servico.php" ><button class="btn">Fazer outro pedido </button></a>
    <a href="cliente.php" ><button class="btn">Página Principal </button></a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php else:header('Location: login.php'); endif; ?> 