<?php
// Conexão com o banco de dados e outras configurações
include_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    // Inserir no banco de dados

    $sql = "INSERT INTO servicos (nome, descricao, preco) VALUES ('$nome', '$descricao', '$preco')";
    $resultado = $conexao->query($sql);
    // Verifique e lide com os erros de inserção se necessário
}

header('Location: cadastrarServico.php');
?>
