<?php
    require_once './verifyadm.php';
    include_once '../conexao.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_cliente = $_POST['id_cliente'];
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $docId = $_POST['docId'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nasc = $_POST['data_nasc'];
        $sexo = $_POST['sexo'];
        $endereco = $_POST['endereco'];
        $senha = md5($_POST['senha']);
        $conf_senha = md5($_POST['conf_senha']);

        $sql = "UPDATE cliente SET nome='$nome', sobrenome='$sobrenome', docId='$docId', email='$email', telefone='$telefone', data_nasc='$data_nasc', sexo='$sexo', endereco='$endereco', senha='$senha', conf_senha='$conf_senha'  WHERE id_cliente=$id_cliente";
        $resultado = $conexao->query($sql);

        if ($resultado) {
            header('Location: ./controlCliente.php'); // Redireciona após atualização
            exit;
        } else {
            echo "Erro ao atualizar cliente: " . $conexao->error;
        }
    }
?>
