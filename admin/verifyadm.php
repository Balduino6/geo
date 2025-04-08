<?php
    session_start();
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['acesso'])) {
        header('location: ../login.php?msg=acesso_negado');
        exit();
    }
    if ($_SESSION['acesso'] !== 'administrador') {
        header('location: ../login.php?msg=acesso_restrito');
        exit();
    }
    $nomeUsuario = $_SESSION['usuario'];

    $tipoAcesso = $_SESSION['acesso'];
    // $sobrenome = $_SESSION['sobrenome'];

?>