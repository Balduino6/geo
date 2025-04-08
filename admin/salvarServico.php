<?php
require_once '../conexao.php';

// Recebe os dados do formulário
$id_categoria = $_POST['id_categoria'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];

// Processa o upload da imagem
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    $imagem = $_FILES['imagem'];
    // Cria um nome único para a imagem
    $nomeArquivo = time() . '_' . $imagem['name'];
    // Define o destino na pasta upload/servicos
    $destino = '../upload/servicos/' . $nomeArquivo;
    if (!move_uploaded_file($imagem['tmp_name'], $destino)) {
        die('Erro ao mover o arquivo de imagem.');
    }
} else {
    die('Imagem não enviada.');
}

// Insere os dados na tabela Servicos
$sql = "INSERT INTO Servicos (id_categoria, nome, descricao, preco, imagem) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("issds", $id_categoria, $nome, $descricao, $preco, $nomeArquivo);
if ($stmt->execute()) {
    header("Location: cadastrarServico.php?msg=Serviço cadastrado com sucesso!");
} else {
    echo "Erro: " . $stmt->error;
}
?>
