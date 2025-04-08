<?php 
    require 'conexao2.php';
    if(isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente'])){
        require_once './usuarioClass.php';

        //instanciando o usuario
        $u = new Usuario();
        
        //importar as informacoes da funcao logged
        $listLogged = $u->logged ($_SESSION['id_cliente']);
        $nomeUser = $listLogged['nome'];
        $sobrenome =$listLogged['sobrenome'];

    }else{
        header("Location: ./login.php");
    }
 
?>


