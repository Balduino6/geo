<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Geovane Services</title>
</head>
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
        margin-right: 50px;
    }

    .menu {
        display: flex;
        gap: 20px;
        margin-left: auto;
        margin-right: 20px;
    }

    .menu a {
        color: whitesmoke;
        text-decoration: none;
        font-size: 18px;
        padding: 8px 12px;
        border-radius: 5px;
    }

    .menu a:hover {
        color: #ddd;
        background-color: #444;
    }

    .header {
    background: url(../assets/bodyimg.jpg) no-repeat center center;
    background-size: cover;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    text-align: center;
    border-radius: 10px;
    margin-bottom: 30px;
    margin-top: 50px;
 }

.headline {
    margin-top: 100px;
}

.headline h2 {
    margin: 0;
    font-size: 36px;
}

.headline p {
    font-size: 19px;
}

    

.content {
    margin-top: 80px; /* Adiciona espaço para o menu superior */
    padding: 20px;
    flex-grow: 1;
    
}

    /* Outros estilos já presentes */
    .about-section {
        padding: 40px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 90%;
        max-width: 1040px;
        margin-left: auto;
        margin-right: auto;
    }

    .mission, .values {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        width: 90%;
        max-width: 1040px;
        margin: 20px auto;      
        
    }

    .cop {
        /* background-color: #222; */
        color: #444;
        text-align: center;
        padding: 20px;
        width: 100%;
        border-top: solid 1px #ddd;
        margin-top:100px;
    }

    .whats {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .whats img {
        width: 50px;
        height: 50px;
    }

    footer {
        margin-top: 200px;
      background-color: #333;
      color: white;
      text-align: center;
      padding: 20px;
      
    }

    /* Estilos do Chatbot */
    .chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        background-color: #f1f1f1;
        border: 1px solid #333;
        border-radius: 10px;
        display: none;
        flex-direction: column;
        z-index: 1001;
    }

    .chatbot-header {
        background-color: #333;
        color: white;
        padding: 10px;
        text-align: center;
        position: relative;
    }

    .chatbot-header button {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: transparent;
        color: white;
        border: none;
        font-size: 16px;
        cursor: pointer;
    }

    .chatbot-body {
        padding: 10px;
        max-height: 300px;
        overflow-y: auto;
    }

    .chatbot-footer {
        padding: 10px;
        display: flex;
        align-items: center;
    }

    .chatbot-footer input {
        flex-grow: 1;
        padding: 5px;
        margin-right: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .chatbot-footer button {
        padding: 5px 10px;
        background-image: linear-gradient(to right, #555 , #444);
        color: white;
        border-radius:50px;
        cursor: pointer;
    }

    .chatbot-footer button:hover{
        background-image: linear-gradient(to right, #444 , #555);
    }

    .bot-message {
        background-color: #e0e0e0;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .user-message {
        background-color: #333;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        text-align: right;
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

<script>
    function toggleMenu() {
        document.querySelector('.menu').classList.toggle('active');
    }
</script>

<body>
<div class="topbar">
    <div class="logo">
        <img src="./assets/logo.png" alt="Logo">
    </div>
    <div class="menu">
        <a href="index.php">Principal</a>
        <a href="sobre.php">Sobre Nós</a>
        <a href="#">Serviços</a>
        <a href="cliente/registroCliente.php">Regista-se</a>
        <a href="cliente/login.php">Login</a>
    </div>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</div>

    <div class="content">

    <header class="header">
        <div class="headline">
            <h2>Bem-Vindo à GEOVANE SERVICES</h2>
            <p>Funcionalidade é a nossa função</p>
        </div>
    </header>

        <section class="about-section">
            <h2>Sobre Nós</h2>
            <img src="./assets/logo.png" alt="Nossa Empresa" style="width: 250px; height:150px; border-radius:5%;">
            <p>Fundada em 2023, a Geovane Services é uma empresa dedicada à prestação de serviços de TI...</p>
        </section>

        <section class="mission">
            <div>
                <h3>Missão</h3>
                <p>Nossa missão é fornecer soluções tecnológicas inovadoras...</p>
            </div>
        </section>

        <section class="values">
            <div>
                <h3>Valores</h3>
                <ul>
                    <li>Compromisso com a Qualidade</li>
                    <li>Inovação Constante</li>
                    <li>Foco no Cliente</li>
                    <li>Integridade e Transparência</li>
                </ul>
            </div>
            <div>
                <h3>Visão</h3>
                <p>Ser reconhecida como a principal empresa de soluções tecnológicas na região...</p>
            </div>
        </section>
    </div>

    <footer>
        <p>© Todos direitos reservados por GeovaneServices</p>
    </footer>

</body>
</html>
