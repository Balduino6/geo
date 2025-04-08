<?php
require_once './verifyadm.php';
include_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria_nome = trim($_POST['categoria_nome']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);

    if(empty($categoria_nome)){
        header("Location: cadastrarServico.php?msg=Categoria+invalida");
        exit;
    }

    $stmt = $conexao->prepare("INSERT INTO categorias (nome, descricao) VALUES (?,?)");
    $stmt->bind_param("ss", $categoria_nome, $descricao);

    if ($stmt->execute()) {
        header("Location: cadastrarServico.php?msg=Categoria+cadastrada+com+sucesso");
    } else {
        header("Location: cadastrarServico.php?msg=Erro+ao+cadastrar+categoria");
    }
    $stmt->close();
} else {
    header("Location: cadastrarServico.php");
    exit;
}
?>
