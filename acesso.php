<?php
include_once('conexao.php');

if (isset($_POST['submit'])) {
    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);
    $acesso = $_POST['acesso'];
    
    // Verifica se o tipo de acesso é válido
    if ($acesso == 'funcionario' || $acesso == 'administrador') {
        $query = "SELECT * FROM funcionario WHERE usuario = '$usuario' AND senha = '$senha' AND acesso = '$acesso'";
        $resultado = mysqli_query($conexao, $query);
        $row = mysqli_fetch_assoc($resultado);

        if ($row) {
            session_start();
            // Armazena o ID e os demais dados do funcionário na sessão
            $_SESSION['id_funcionario'] = $row['id_funcionario']; // Certifique-se de que a coluna seja 'id_funcionario'
            $_SESSION['usuario'] = $usuario;
            $_SESSION['acesso'] = $acesso;

            if ($acesso == 'funcionario') {
                header('location: funcionario/funcionario.php'); // Página do funcionário
            } elseif ($acesso == 'administrador') {
                header('location: admin/dashboard_admin.php'); // Página do administrador
            }
            exit;
        } else {
            header('location: login.php?msg=credenciais_incorretas');
            exit;
        }
    } else {
        header('location: login.php?msg=tipo_acesso_invalido');
        exit;
    }
} else {
    header('location: login.php'); // Redireciona se não for uma solicitação POST
    exit;
}
?>
