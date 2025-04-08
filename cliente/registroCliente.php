<?php 
    $msg = $_GET['msg'] ?? '';

    if ($msg == 'credenciais_incorretas') {
        $mensagem = "Credenciais incorretas. Por favor, tente novamente!";
    } elseif ($msg == 'Usuário Inválido') {
        $mensagem = "Usuário Inválido, Por Favor, tente novamente!";
    } else {
        $mensagem = "";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Geovane Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<style>
/* Estilos gerais */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    background-color: #f1f1f1;
}

.topbar {
    width: 100%;
    background-color: #333;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    z-index: 1000;
}

.topbar .logo img {
    width: 150px;
    border-radius: 5%;
}

.menu-icon {
    display: none;
    font-size: 30px;
    color: white;
    cursor: pointer;
    margin-right: 20px;
}

.menu {
    display: flex;
    gap: 20px;
    margin-left: auto;
}

.menu a {
    color: whitesmoke;
    text-decoration: none;
    font-size: 18px;
    padding: 8px 12px;
    border-radius: 5%;
}

.menu a:hover {
    color: #ddd;
    background-color: #444;
    transition: .3s;
    border-bottom-left-radius: 6%;
}

.header {
    margin-top: 250px;
    text-align: center;
}

.table-bg {
    background: rgba(0,0,0,0.5);
    border-radius: 20px;
}

form{
        text-align:left;
    }

.conta{
        text-decoration: none;
        font-size: 15px;
        color:#180F4A;
        font-family: Arial, Helvetica, sans-serif;
        padding: 5px;
    }

    .conta:hover{
        color: #2209af;
    }

button{
        background-image: linear-gradient(to right,  #555, #444);
    }

    button:hover{
        background-image: linear-gradient(to right, #444 , #555);

        outline: none;
    }

    .btn{
        color: white;
    }

    .btn:hover{
        color: white;
    }   

    footer {
        margin-top: 200px;
      background-color: #333;
      color: white;
      text-align: center;
      padding: 20px;
      
    }

@media (max-width: 768px) {
    .menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 80px;
        right: 0;
        background-color: #333;
        width: 50%;
        text-align: center;
        border-bottom-left-radius: 5%;
       
    }

    .menu a {
        padding: 15px 0;
        font-size: 20px;
        transition:.3s;
    }

    .menu.active {
        display: flex;
    }

    .menu-icon {
        display: block;
    }
}
</style>

<div class="topbar">
    <div class="logo">
        <img src="../assets/logo.png" alt="Logo">
    </div>
    <div class="menu-icon" onclick="toggleMenu()">
        <i id="menuIcon" class="bi bi-list"></i>
    </div>
    <div class="menu">
        <a href="../index.php">Principal</a>
        <a href="../sobre.php">Sobre Nós</a>
        <a href="#">Serviços</a>
        <a href="./registroCliente.php">Regista-se</a>
        <a href="./login.php">Login</a>
    </div>
</div>

<script>
    function toggleMenu() {
        document.querySelector('.menu').classList.toggle('active');
    }
</script>

<div class="content">
    <header class="header">
        <div class="container">
        <h1 class="title">Registro de Clientes</h1>
            <form action="cadastrarCli.php" method="post" enctype="multipart/form-data">
                <!-- Campos do formulário -->
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome*</label>
                    <input type="text" name="nome" class="form-control" placeholder="Digite o seu nome" required>
                </div>

                <div class="mb-3">
                    <label for="sobrenome" class="form-label">Sobrenome*</label>
                    <input type="text" name="sobrenome" class="form-control" placeholder="Digite o seu sobrenome" required>
                </div>

                <div class="mb-3">
                    <label for="docId" class="form-label">Documento de Identificação*</label>
                    <input type="text" name="docId" class="form-control" placeholder="xxxxxxxxxLAxxx" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@gmail.com" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone*</label>
                    <input type="text" name="telefone" class="form-control" placeholder="Número de telefone" required>
                </div>

                <div class="mb-3">
                    <label for="data_nasc" class="form-label">Data de Nascimento*</label>
                    <input type="date" name="data_nasc" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="sexo" class="form-label">sexo</label>
                    <select name="sexo" id="sexo" class="form-control" required >
                        <option selected disabled="" value="Selecione o seu sexo" >Selecione o seu sexo ></option>
                    
                        <option value="masculino" name="sexo" class="form-control">Masculino</option>
                    
                        <option value="feminino" name="sexo" class="form-control">Feminino</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço*</label>
                    <input type="text" name="endereco" class="form-control" placeholder="Digite um endereço" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha*</label>
                    <input type="password" name="senha" placeholder="No mínimo 4 caracteres" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="conf_senha" class="form-label">Confirmar senha*</label>
                    <input type="password" name="conf_senha" class="form-control" placeholder="Repita a mesma senha" required>
                </div>
                                   
                <div class="mb-3">
                    <label for="imagem" class="form-label">Imagem de Perfil*</label>
                    <input type="file" name="imagem" accept="image/*" class="form-control" required>
                </div>

                <button type="submit" class="btn">Registrar</button>
                <button type="reset" class="btn" name="reset">Apagar</button>
                <button type="button" class="btn" onclick="window.location.href='funcionario.php' ">Cancelar</button>
                <br><br>
                <a class="conta" href="login.php">Já tem uma conta? Faça o login!</a>
            </form>
        </div>
    </header>
</div>

<footer>
    <p>© Todos direitos reservados por GeovaneServices</p>
</footer>

 
</body>
</html>
