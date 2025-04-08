<?php 
    session_start();
    unset($_SESSION['usuario']);
    unset($_SESSION['acesso']);
    
    header("Location: ../login.php");
?>