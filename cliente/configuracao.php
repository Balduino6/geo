<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Conta - Geovane Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group img {
            max-width: 100px;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        .form-group input[type="file"] {
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            color: white;
            background-color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #555;
        }

        .btn-cancel {
            background-color: #999;
        }

        .btn-cancel:hover {
            background-color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Configurações de Conta</h2>
    <form action="atualizar_dados.php" method="POST" enctype="multipart/form-data">
        <!-- Alterar Nome -->
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="Usuário Atual">
        </div>

        <!-- Alterar Email -->
        <div class="form-group">
            <label for="email">Endereço de Email</label>
            <input type="email" id="email" name="email" value="usuario@example.com">
        </div>

        <!-- Alterar Fotografia -->
        <div class="form-group">
            <label for="foto">Alterar Fotografia</label><br>
            <img src="./assets/user-avatar.png" alt="Fotografia Atual">
            <input type="file" id="foto" name="foto">
        </div>

        <!-- Alterar Palavra-Passe -->
        <div class="form-group">
            <label for="senha">Nova Palavra-Passe</label>
            <input type="password" id="senha" name="senha">
        </div>

        <div class="form-group">
            <label for="confirmar_senha">Confirmar Nova Palavra-Passe</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha">
        </div>

        <!-- Botões -->
        <div class="form-actions">
            <button type="submit" class="btn">Guardar Alterações</button>
            <button type="button" class="btn btn-cancel" onclick="window.location.href='cliente.php'">Cancelar</button>
        </div>
    </form>
</div>

</body>
</html>
