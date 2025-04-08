<?php

require_once 'verifyadm.php';
require_once '../conexao.php';

// Se o formulário for enviado via POST, atualiza as permissões do funcionário.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $permitted_menus = isset($_POST['menus']) ? $_POST['menus'] : [];
    // Codifica as permissões em JSON.
    $jsonPermissions = json_encode($permitted_menus);
    
    // Atualiza o campo 'menu_permissions' na tabela Funcionario.
    $stmt = $conexao->prepare("UPDATE Funcionario SET menu_permissions = ? WHERE id_funcionario = ?");
    $stmt->bind_param("si", $jsonPermissions, $employee_id);
    if ($stmt->execute()) {
        $msg = "Permissões atualizadas com sucesso.";
    } else {
        $msg = "Erro ao atualizar permissões.";
    }
}

// Recupera a lista de funcionários para seleção.
$employees = [];
$result = $conexao->query("SELECT id_funcionario, nome FROM Funcionario");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Seleciona um funcionário, se nenhum for escolhido, usa o primeiro.
$selected_employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : (count($employees) > 0 ? $employees[0]['id_funcionario'] : 0);

// Recupera as permissões atuais para o funcionário selecionado.
$current_permissions = [];
if ($selected_employee_id) {
    $stmt = $conexao->prepare("SELECT menu_permissions FROM Funcionario WHERE id_funcionario = ?");
    $stmt->bind_param("i", $selected_employee_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        // Tratamento para evitar que json_decode() receba NULL
        $current_permissions = json_decode($row['menu_permissions'] ?? "[]", true);
    }
}

// Define os menus disponíveis para o sistema.
$available_menus = [
    "dashboard"            => "Dashboard",
    "chat_funcionario"     => "Chat",
    "enviar_notificacoes"  => "Notificações",
    "tickets"              => "Tickets",
    "transacoes_admin"     => "Transações",
    "saldo_clientes"       => "Saldo dos Clientes",
    "controlFunci"         => "Controle de Funcionário",
    "controlCliente"       => "Controle de Clientes",
    "exibirServicos"       => "Ver Serviços",
    "cadastrarServico"     => "Registrar Serviços",
    "registroFun"          => "Registrar Funcionário",
    "registroCliente"      => "Registrar Cliente",
    "relatorios"           => "Relatórios",
    "gerar_voucher"        => "Gerar Voucher",
    "pedidos"              => "Pedidos de Serviços",
    "configuracao"         => "Configurações",
    "permissoes"           => "Permissões",
    "movimentos_cliente"   => "Movimentos dos clientes",
    "ver_notificacoes"     =>"Ver Notificações",
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Configurar Permissões</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 5px;
            padding: 30px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        button{
        background-image: linear-gradient(to right,  #555, #444);
        color: white;
    }

    button:hover{
        background-image: linear-gradient(to right, #444 , #555);
        color: white;
        outline: none;
    }

    .btn{
        color: white;
    }

    .btn:hover{
        color: white;
    }
    </style>
</head>
<body>
<div class="container">
    <h1>Configurar Permissões</h1>
    
    <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
    <?php endif; ?>

    <!-- Formulário para selecionar o funcionário -->
    <form method="GET" action="">
        <div class="mb-3">
            <label for="employee_id" class="form-label">Selecione o Funcionário:</label>
            <select class="form-select" name="employee_id" id="employee_id" onchange="this.form.submit()">
                <?php foreach($employees as $emp): ?>
                    <option value="<?php echo $emp['id_funcionario']; ?>" <?php echo ($emp['id_funcionario'] == $selected_employee_id ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($emp['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Formulário para atualizar as permissões -->
    <form method="POST" action="">
        <input type="hidden" name="employee_id" value="<?php echo $selected_employee_id; ?>">
        <h3>Selecione os menus permitidos:</h3>
        <div class="mb-3">
            <?php foreach($available_menus as $key => $label): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="menus[]" value="<?php echo $key; ?>" id="menu_<?php echo $key; ?>"
                    <?php echo (is_array($current_permissions) && in_array($key, $current_permissions)) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="menu_<?php echo $key; ?>"><?php echo $label; ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn">Atualizar Permissões</button>
        <button type="button" class="btn" onclick="window.location.href='dashboard_admin.php' ">Voltar</button>
    </form>
</div>
</body>
</html>
