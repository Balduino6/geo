<?php
    // Verifica se o id_cliente foi passado como parâmetro na URL
    if(isset($_GET['id_funcionario']) && !empty($_GET['id_funcionario'])){
        // Inclua o arquivo de conexão com o banco de dados
        require_once '../conexao.php';

        // Prepare uma declaração DELETE
        $sql = "DELETE FROM funcionario WHERE id_funcionario = ?";

        if($stmt = $conexao->prepare($sql)){
            // Vincule as variáveis à declaração preparada como parâmetros
            $stmt->bind_param("i", $param_id);

            // Defina os parâmetros
            $param_id = $_GET['id_funcionario'];

            // Execute a declaração preparada
            if($stmt->execute()){
                // Redirecione de volta para a página de controle de clientes após excluir o cliente
                header("location: controlFunci.php");
                exit();
            } else{
                echo "Algo deu errado. Por favor, tente novamente mais tarde.";
            }
        }

        // Feche a declaração preparada
        $stmt->close();

        // Feche a conexão
        $conexao->close();
    } else{
        // Se o id_cliente não estiver presente na URL, redirecione o usuário de volta para a página de controle de clientes
        header("location: controlFuncionario.php");
        exit();
    }
?>