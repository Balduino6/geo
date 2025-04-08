<?php 
require 'conexao.php';


// Consulta os serviços do banco de dados
$sqlServicos = "SELECT id_servico, nome, preco, descricao, imagem FROM Servicos";
$resultServicos = $conexao->query($sqlServicos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Geovane Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
     <!-- icons  -->
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
    /* font-size: 18px; */
    /* padding: 8px 12px; */
    border-radius: 5px;

    font-size: 16px; /* Diminuindo um pouco o tamanho da fonte */
    padding: 6px 10px; /* Ajustando o espaçamento */
}

.menu a:hover {
    color: #ddd;
    background-color: #444;
}

.content {
    margin-top: 80px; /* Adiciona espaço para o menu superior */
    padding: 20px;
    flex-grow: 1;
    overflow-y: auto;
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


/* Cards de Serviços Detalhados - Grid Responsivo */
.servicos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1040px;
            margin: 0 auto 30px;
        }
        .servico-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .servico-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .servico-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .servico-detalhes {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .servico-detalhes h3 {
            margin-top: 0;
            font-size: 20px;
        }
        .servico-detalhes p {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .btn-pedir-servico {
            font-size: 14px;
            padding: 8px 16px;
            align-self: flex-start;
        }

footer {
    background-color: #333;
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
    bottom: 0;
    width: 100%;
    height: 260px;
    margin-top: 300px;
}

.social-links {
    padding: 5px;
}

.social-links img {
    width: 30px;
    height: 30px;
    display: inline;
    align-items: left;
    justify-content: left;
    transition: 0.2s;
    border-radius: 50%;
    padding: 10px;
}

.social-links img:hover {
    padding: 20px;
    background: #575757;
}

.qr {
    margin-top: -20px;
    margin-right: 1300px;
}

.qr img {
    width: 90px;
    height: 90px;
}

.cop {
    background-color: #222;
    color: white;
    text-align: center;
    padding: 20px;
    width: 100%;
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

    /* Container do Chatbot */
    .chatbot-container {
       position: fixed;
       bottom: 20px;
       right: 20px;
       width: 350px;
       background-color: #fff;
       box-shadow: 0 2px 10px rgba(0,0,0,0.1);
       border-radius: 10px;
       display: none;
       flex-direction: column;
       overflow: hidden;
       animation: fadeIn 0.5s;
       font-family: Arial, sans-serif;
    }
    /* Cabeçalho */
    .chatbot-header {
       background-color: #007bff;
       color: #fff;
       padding: 15px;
       display: flex;
       justify-content: space-between;
       align-items: center;
    }
    .chatbot-header h3 {
       margin: 0;
       font-size: 16px;
    }
    .chatbot-header button {
       background: transparent;
       border: none;
       color: #fff;
       font-size: 16px;
       cursor: pointer;
    }
    /* Corpo do Chat */
    .chatbot-body {
       padding: 15px;
       height: 300px;
       overflow-y: auto;
       background-color: #f9f9f9;
    }
    /* Rodapé com input */
    .chatbot-footer {
       display: flex;
       border-top: 1px solid #eee;
    }
    .chatbot-footer input {
       flex: 1;
       padding: 10px;
       border: none;
       outline: none;
       font-size: 14px;
    }
    .chatbot-footer button {
       padding: 10px 15px;
       border: none;
       background-color: #007bff;
       color: #fff;
       cursor: pointer;
       font-size: 14px;
    }
    /* Mensagens do Bot e do Usuário */
    .bot-message,
    .user-message {
       padding: 10px;
       margin-bottom: 10px;
       border-radius: 5px;
       max-width: 80%;
       clear: both;
       word-wrap: break-word;
    }
    .bot-message {
       background-color: #e9ecef;
       color: #333;
       float: left;
    }
    .user-message {
       background-color: #007bff;
       color: #fff;
       float: right;
       text-align: right;
    }
    /* Animação de aparição */
    @keyframes fadeIn {
       from { opacity: 0; transform: translateY(20px); }
       to { opacity: 1; transform: translateY(0); }
    }
    /* Botão para abrir o chatbot */
    .whats {
       position: fixed;
       bottom: 20px;
       right: 20px;
       background-color: #007bff;
       width: 50px;
       height: 50px;
       border-radius: 50%;
       display: flex;
       align-items: center;
       justify-content: center;
       box-shadow: 0 2px 10px rgba(0,0,0,0.2);
       cursor: pointer;
    }
    .whats img {
       width: 30px;
       height: 30px;
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
        <a href="cliente/registroCliente.php">Regista-se</a>
        <a href="cliente/login.php">Login</a>
    </div>
</div>

<script>
    function toggleMenu() {
        document.querySelector('.menu').classList.toggle('active');
    }
</script>

<div class="content">
    <header class="header">
        <div class="headline">
            <h2>Bem-Vindo à GEOVANE SERVICES</h2>
            <p>Funcionalidade é a nossa função</p>
        </div>
    </header>

     <!-- Seção de Serviços Detalhados -->
     <section class="servicos">
            <?php if($resultServicos && $resultServicos->num_rows > 0): ?>
                <?php while($servico = $resultServicos->fetch_assoc()): ?>
                    <div class="servico-card">
                        <img src="../upload/servicos/<?php echo $servico['imagem']; ?>" alt="<?php echo htmlspecialchars($servico['nome']); ?>">
                        <div class="servico-detalhes">
                            <h3><?php echo htmlspecialchars($servico['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
                            <p><strong>Preço:</strong> <?php echo number_format($servico['preco'], 2, ',', '.'); ?> Kz</p>
                            <!-- <a href="pedido_servico.php?servico=<?php echo $servico['id_servico']; ?>" class="btn btn-primary btn-pedir-servico">Pedir Serviço</a> -->
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Nenhum serviço cadastrado.</p>
            <?php endif; ?>
        </section>

    <footer>
        <h2>Contactos</h2>
        <p>GEOVANE SERVICES</p>
        <p>Futungo de Belas, Luanda</p>
        <p>Tel: 933416260</p>
        <p>Email: geovaneservices@gmail.com</p>
        <div class="social-links">
            <a href="#"><img src="./assets/face.png" alt="Facebook"></a>
            <a href="#"><img src="./assets/insta.png" alt="Instagram"></a>
            <a href="https://youtube.com/maykbrito"><img src="./assets/you.png" alt="YouTube"></a>
        </div>
        <div class="qr">
            <img src="./assets/qr.png" alt="QR Code">
        </div>
    </footer>

    <div class="cop">
        <p class="copy">© Todos direitos reservados por <b>GeovaneServices</b></p>
    </div>
  
 <!-- Chatbot -->
<div class="chatbot-container" id="chatbot">
    <div class="chatbot-header">
        <h3>Geovane Services - Assistente Virtual</h3>
        <button onclick="closeChatbot()">X</button>
    </div>
    <div class="chatbot-body" id="chat-body">
        <div class="bot-message">
            <p>Olá! Como posso ajudar você com os serviços da Geovane Services?</p>
        </div>
    </div>
    <div class="chatbot-footer">
        <input type="text" id="user-input" placeholder="Digite sua mensagem..." onkeypress="if(event.key === 'Enter'){sendMessage();}">
        <button onclick="sendMessage()">Enviar</button>
    </div>
</div>

<!-- Botão de abrir o Chatbot -->
<a class="whats" href="javascript:void(0)" onclick="openChatbot()" title="Assistente Virtual">   
   <img src="assets/chat3.png" alt="Chat">
</a>

<script>
  // Abrir e fechar o chatbot
  function openChatbot() {
      document.getElementById('chatbot').style.display = 'flex';
  }

  function closeChatbot() {
      document.getElementById('chatbot').style.display = 'none';
  }

  // Função para remover acentos e pontuação do input
  function normalizeInput(input) {
      // Remove acentos
      input = input.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      // Remove pontuação
      input = input.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, "");
      // Remove espaços em excesso
      input = input.replace(/\s{2,}/g, " ");
      return input.trim().toLowerCase();
  }

  // Enviar mensagem
  function sendMessage() {
      let userInputElement = document.getElementById('user-input');
      let userInput = normalizeInput(userInputElement.value);
      if (userInput === '') return;

      // Exibir mensagem do usuário
      let userMessageHTML = `<div class="user-message"><p>${userInput}</p></div>`;
      document.getElementById('chat-body').innerHTML += userMessageHTML;
      userInputElement.value = '';

      // Salvar mensagem do usuário
      saveConversation(userInput, 'user');

      // Responder após um pequeno atraso
      setTimeout(() => {
          let botResponse = getBotResponse(userInput);
          let botMessageHTML = `<div class="bot-message"><p>${botResponse}</p></div>`;
          document.getElementById('chat-body').innerHTML += botMessageHTML;

          // Salvar resposta do bot
          saveConversation(botResponse, 'bot');

          // Atualizar scroll para a última mensagem
          let chatBody = document.getElementById('chat-body');
          chatBody.scrollTop = chatBody.scrollHeight;
      }, 500);
  }

  // Função para salvar a conversa (exige implementação no lado do servidor)
  function saveConversation(message, sender) {
      const data = { message, sender };
      fetch('save_conversation.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
      }).catch(error => console.error('Erro ao salvar conversa:', error));
  }

  // Função que retorna a resposta do bot com base na entrada do usuário
  function getBotResponse(input) {
      // Dicionário expandido com variações e sinônimos
      const responses = {
          'serviços': 'Oferecemos serviços como desenvolvimento de sites, montagem de redes LANs, manutenção de computadores, consultoria em TI, entre outros.',
          'desenvolvimento de sites': 'Desenvolvemos sites profissionais sob medida. Entre em contato para mais detalhes.',
          'site': 'Desenvolvemos sites profissionais sob medida. Entre em contato para mais detalhes.',
          'montagem de redes': 'Podemos montar redes locais para sua empresa com toda a infraestrutura necessária.',
          'redes': 'Podemos montar redes locais para sua empresa com toda a infraestrutura necessária.',
          'contato': 'Você pode nos contatar pelo telefone 933416260 ou pelo e-mail geovaneservices@gmail.com.',
          'manutenção de computadores': 'Oferecemos serviços completos de manutenção de computadores.',
          'manutenção': 'Oferecemos serviços completos de manutenção de computadores.',
          'consultoria': 'Realizamos consultoria em TI para ajudar sua empresa a melhorar processos e infraestrutura.', 'como vai': 'vou bem, em que posso ajudar?', 'oi':'olá em que posso ser útil?', 'muito obrigado':'De nada, agradeçemos pela preferência', 'obrigado':'De nada, agradeçemos pela preerência', 'obrigada':'De nada, agradeçemos pela preerência', 'muito obrigada':'De nada, agradeçemos pela preerência'
      };

      // Tenta encontrar correspondência direta
      if (responses[input]) {
          return responses[input];
      }

      // Utiliza correção de erros para encontrar a palavra-chave mais próxima
      const possibleWords = Object.keys(responses);
      const correctedInput = correctSpelling(input, possibleWords);
      if (responses[correctedInput]) {
          return responses[correctedInput];
      }

      return 'Desculpe, não entendi sua pergunta. Por favor, pergunte sobre nossos serviços ou entre em contato conosco.';
  }

  // Corrige erros de digitação com base na distância de Levenshtein
  function correctSpelling(input, possibleWords) {
      let closestMatch = '';
      let lowestDistance = Infinity;

      possibleWords.forEach(word => {
          let distance = levenshteinDistance(input, word);
          if (distance < lowestDistance) {
              closestMatch = word;
              lowestDistance = distance;
          }
      });

      // Agora o threshold foi ajustado para até 4
      if (lowestDistance <= 4) {
          return closestMatch;
      }
      return input;
  }

  // Algoritmo de distância de Levenshtein
  function levenshteinDistance(a, b) {
      const matrix = [];
      for (let i = 0; i <= b.length; i++) {
          matrix[i] = [i];
      }
      for (let j = 0; j <= a.length; j++) {
          matrix[0][j] = j;
      }
      for (let i = 1; i <= b.length; i++) {
          for (let j = 1; j <= a.length; j++) {
              if (b.charAt(i - 1) === a.charAt(j - 1)) {
                  matrix[i][j] = matrix[i - 1][j - 1];
              } else {
                  matrix[i][j] = Math.min(
                      matrix[i - 1][j - 1] + 1,
                      Math.min(matrix[i][j - 1] + 1, matrix[i - 1][j] + 1)
                  );
              }
          }
      }
      return matrix[b.length][a.length];
  }
</script>
</body>
</html>