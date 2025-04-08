<?php
require_once './verifyadm.php';
include_once '../conexao.php';

// Verifica se o ID do serviço foi fornecido
if (isset($_GET['id_servico'])) {
    $id_servico = intval($_GET['id_servico']);
    $sql = "SELECT * FROM servicos WHERE id_servico = $id_servico";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        $servico = $resultado->fetch_assoc();
    } else {
        echo "Serviço não encontrado.";
        exit;
    }
} else {
    echo "ID do serviço não especificado.";
    exit;
}

// Busca as categorias disponíveis
$cat_sql = "SELECT id_categoria, nome FROM categorias ORDER BY nome ASC";
$cat_result = $conexao->query($cat_sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        form {
            width: 30%;
            margin: 0 auto;
            margin-top: 100px;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f1f1f1;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .cop {
            color: #444;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="content">
        <header class="header">
            <h1>Editar Serviço</h1>
        </header>
        <!-- O formulário passa a utilizar enctype para upload de arquivos -->
        <form action="actualizarServico.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_servico" value="<?php echo $servico['id_servico']; ?>">
            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoria</label>
                <select class="form-control" id="id_categoria" name="id_categoria" required>
                    <option value="" disabled>Selecione uma categoria</option>
                    <?php 
                    // Reexecuta a query para categorias, caso não esteja disponível
                    $cat_result = $conexao->query("SELECT id_categoria, nome FROM categorias ORDER BY nome ASC");
                    while($cat = $cat_result->fetch_assoc()):
                        // Se o serviço já tem uma categoria, preseleciona a mesma
                        $selected = ($cat['id_categoria'] == $servico['id_categoria']) ? "selected" : "";
                    ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Serviço</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($servico['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?php echo htmlspecialchars($servico['descricao']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço (Kz)</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?php echo $servico['preco']; ?>" required>
            </div>
            <!-- Exibe a imagem atual do serviço -->
            <div class="mb-3">
                <label class="form-label">Imagem Atual do Serviço</label>
                <div>
                    <img src="../upload/servicos/<?php echo $servico['imagem']; ?>" alt="Imagem do Serviço" class="img-fluid" style="max-width: 200px;">
                </div>
            </div>
            <!-- Campo para atualizar a imagem (opcional) -->
            <div class="mb-3">
                <label for="imagem" class="form-label">Atualizar Imagem (opcional)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='exibirServicos.php'">Voltar</button>
        </form>
    </div>
</body>
</html>
