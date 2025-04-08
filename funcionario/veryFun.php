<?php
    session_start();
    // Verificação de acesso para funcionário
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['acesso'])) {
        header('location: ../login.php?msg=acesso_negado');
        exit();
    }
    if ($_SESSION['acesso'] !== 'funcionario') {
        header('location: ../login.php?msg=acesso_restrito');
        exit();
    }
    
    $nomeUsuario = $_SESSION['usuario'];
        $tipoAcesso = $_SESSION['acesso'];
        // $sobrenome = $_SESSION['sobrenome'];

    ?>

