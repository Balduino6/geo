<?php
    session_start();
    require_once './veryFun.php';
    include_once '../conexao.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_funcionario'])) {
        $id_funcionario = $_SESSION['id_funcionario'];
        
        $email = $_POST['email'];
        $usuario = $_POST['usuario'];
        $senha = !empty($_POST['senha']) ? md5($_POST['senha']) : null;
        $conf_senha = !empty($_POST['conf_senha']) ? md5($_POST['conf_senha']) : null;
    
        // Verificar se as senhas correspondem
        if ($senha === $conf_senha) {
            // Processar a imagem e a atualização do perfil
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
 
                        email='$email', 
                         
                        usuario='$usuario', 
                        senha='$senha', 
                        conf_senha='$conf_senha', 
                         
                        imagem_perfil='$nomeUnico' 
                        WHERE id_funcionario=$id_funcionario";
                
                    $sql = $conexao->query($sql);
                
                    if ($sql) {
                        header('Location: funcionario.php');
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
            header('Location: configuracao.php?error=senhas_nao_correspondem');
            exit;
        }
    } 
  
?>
