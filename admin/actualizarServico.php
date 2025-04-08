<?php
require_once './verifyadm.php';
include_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servico = intval($_POST['id_servico']);
    $id_categoria = intval($_POST['id_categoria']);
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);

    // Validação simples
    if (empty($nome) || empty($descricao) || $preco <= 0) {
        header("Location: editarServico.php?id_servico=$id_servico&msg=Dados+inválidos");
        exit;
    }

    // Verifica se um novo arquivo de imagem foi enviado
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem = $_FILES['imagem'];
        $nomeArquivo = time() . '_' . $imagem['name'];
        $destino = '../upload/servicos/' . $nomeArquivo;
        
        if (!move_uploaded_file($imagem['tmp_name'], $destino)) {
            header("Location: editarServico.php?id_servico=$id_servico&msg=Erro+ao+atualizar+imagem");
            exit;
        }
        
        // Atualiza os dados do serviço, incluindo a nova imagem
        $stmt = $conexao->prepare("UPDATE servicos SET id_categoria = ?, nome = ?, descricao = ?, preco = ?, imagem = ? WHERE id_servico = ?");
        $stmt->bind_param("issdsi", $id_categoria, $nome, $descricao, $preco, $nomeArquivo, $id_servico);
    } else {
        // Atualiza sem modificar a imagem
        $stmt = $conexao->prepare("UPDATE servicos SET id_categoria = ?, nome = ?, descricao = ?, preco = ? WHERE id_servico = ?");
        $stmt->bind_param("issdi", $id_categoria, $nome, $descricao, $preco, $id_servico);
    }

    if ($stmt->execute()) {
        header("Location: exibirServicos.php?msg=Serviço+atualizado+com+sucesso");
    } else {
        header("Location: editarServico.php?id_servico=$id_servico&msg=Erro+ao+atualizar+serviço");
    }
    $stmt->close();
} else {
    header("Location: exibirServicos.php");
    exit;
}
?>
