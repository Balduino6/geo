<?php
    session_start();

    // Verifica se a sessão de usuário está ativa
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['acesso'])) {
        header('location: loginFun.php?msg=acesso_negado');
        exit(); // Encerra o script
        unset($_SESSION['usuario']);
        unset($_SESSION['acesso']);
    // header('Location: loginFun.php?msg=acesso_negado');
    }

    // Verifica se o acesso é de administrador
    if ($_SESSION['acesso'] !== 'administrador') {
        header('location: loginFun.php?msg=acesso_restrito');
        exit(); // Encerra o script
    }

    $nomeUsuario = $_SESSION['usuario'];
    $tipoAcesso = $_SESSION['acesso'];
    // $sobrenome = $_SESSION['sobrenome'];

    ?>