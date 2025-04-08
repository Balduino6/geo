<?php
    // Verifica se o id_servico foi passado como parâmetro na URL
    if(isset($_GET['id_servico']) && !empty($_GET['id_servico'])){
        // Inclua o arquivo de conexão com o banco de dados
        require_once '../conexao.php';

        // Prepare uma declaração DELETE
        $sql = "DELETE FROM servicos WHERE id_servico = ?";

        if($stmt = $conexao->prepare($sql)){
            // Vincule as variáveis à declaração preparada como parâmetros
            $stmt->bind_param("i", $param_id);

            // Defina os parâmetros
            $param_id = $_GET['id_servico'];

            // Execute a declaração preparada
            if($stmt->execute()){
                // Redirecione de volta para a página de controle de servicos após excluir o servico
                header("location: ./exibirServicos.php");
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
        // Se o id_servico não estiver presente na URL, redirecione o usuário de volta para a página de controle de servicos
        header("location: ./exibirServicos.php");
        exit();
    }
?>
