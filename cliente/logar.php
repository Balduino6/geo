<?php 
    session_start();
    
    //se email for diferente de email vazio faça
    if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['senha']) && !empty($_POST['senha'])){

        require './conexao2.php';
        require './usuarioClass.php';

        $u = new Usuario();

        $email = addslashes($_POST['email']);
        $senha = addslashes($_POST['senha']);

        //executando o método login
        if($u->login($email, $senha) == true){
            if(isset($_SESSION['id_cliente'])){
                header("Location: ./cliente.php");

            }else{
                header('Location: ./login.php?msg=credenciais_incorretas');
            }
        }else{
            header('Location: ./login.php?msg=Usuário Inválido');
        }
    }else{
        header("Location: ./login.php");
    }

?>

