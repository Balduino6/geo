<?php 

    include_once('../conexao.php');

    // dados do formulário:
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $docId = $_POST['docId'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data_nasc = $_POST['data_nasc'];
    $sexo = $_POST['sexo'];
    $endereco = $_POST['endereco'];
    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);
    $conf_senha = md5($_POST['conf_senha']);
    $acesso = $_POST['acesso'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $data_contratacao = $_POST['data_contratacao'];

    // Verificar se as senhas correspondem
    if ($senha == $conf_senha) {

        // Processar o upload da imagem de perfil
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $imagem = $_FILES['imagem'];
            $nomeArquivo = $imagem['name'];
            $caminhoTemporario = $imagem['tmp_name'];

            // Definir o diretório de destino para salvar as imagens
            $diretorioDestino = '../upload/imagens_perfil/';

            // Gerar um nome único para o arquivo para evitar conflitos
            $nomeUnico = time() . '_' . $nomeArquivo;

            // Mover o arquivo para o diretório de destino
            if (move_uploaded_file($caminhoTemporario, $diretorioDestino . $nomeUnico)) {
                // Inserir os dados no banco de dados, incluindo o caminho da imagem
                $inserir = "INSERT INTO funcionario 
                            (nome, sobrenome, docId, email, telefone, data_nasc, sexo, endereco, usuario, senha, conf_senha, acesso, tipo_contrato, data_contratacao, imagem_perfil) 
                            VALUES 
                            ('$nome', '$sobrenome', '$docId', '$email', '$telefone', '$data_nasc', '$sexo', '$endereco', '$usuario', '$senha', '$conf_senha', '$acesso', '$tipo_contrato', '$data_contratacao', '$nomeUnico')";

                $inserir = mysqli_query($conexao, $inserir);

                if ($inserir) {
                    // Redireciona após o sucesso
                    header("Location: registroFun.php?msg=Registrado+com+sucesso");
                } else {
                    echo "msg=Erro+ao+registrar " . mysqli_error($conexao);
                }

            } else {
                echo "msg=Erro+ao+mover+o+arquivo+de+imagem";
            }

        } else {
            echo "msg=Erro+no+upload+da+imagem";
        }

    } else {
        // Redireciona de volta se as senhas não coincidirem
        header('Location: registroFun.php?msg=Senhas+não+correspondem');
    }

?>
