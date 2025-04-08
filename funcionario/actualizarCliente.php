<?php
    require_once './verifyadm.php';
    include_once '../conexao.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_funcionario = $_POST['id_funcionario'];
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
                    // Corrigir o caminho da imagem no SQL
                    $sql = "UPDATE funcionario SET 
                        nome='$nome', 
                        sobrenome='$sobrenome', 
                        docId='$docId', 
                        email='$email', 
                        telefone='$telefone', 
                        data_nasc='$data_nasc', 
                        sexo='$sexo', 
                        endereco='$endereco',  
                        senha='$senha', 
                        conf_senha='$conf_senha',  
                        imagem_perfil='$nomeUnico' 
                        WHERE id_funcionario=$id_funcionario";
                
                    $sql = $conexao->query($sql);
                
                    if ($sql) {
                        header('Location: ./controlCliente.php');
                        exit;
                    } else {
                        echo "Erro ao cadastrar: " . mysqli_error($conexao);
                    }
                } else {
                    echo "Erro ao mover o arquivo de imagem.";
                }

            } else {
                echo "Erro no upload da imagem.";
            }

        } else {
            // Redireciona de volta se as senhas não coincidirem
            header('Location: ./editarCliente.php?error=senhas_nao_correspondem');
        }
    
    }

  
?>
