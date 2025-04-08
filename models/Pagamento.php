<?php
require_once __DIR__ . '/../config/db.php';

class Pagamento {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Adicionar crédito ao cliente
    public function adicionarCredito($cliente_id, $valor) {
        $sql = "INSERT INTO pagamentos (cliente_id, valor, tipo, descricao) VALUES (?, ?, 'crédito', 'Depósito de saldo')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('id', $cliente_id, $valor);
        
        if ($stmt->execute()) {
            return $this->atualizarSaldo($cliente_id, $valor);
        }
        return false;
    }

    // Realizar débito na conta do cliente
    public function realizarDebito($cliente_id, $valor, $descricao) {
        if ($this->consultarSaldo($cliente_id) >= $valor) {
            $sql = "INSERT INTO pagamentos (cliente_id, valor, tipo, descricao) VALUES (?, ?, 'débito', ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ids', $cliente_id, $valor, $descricao);
            
            if ($stmt->execute()) {
                return $this->atualizarSaldo($cliente_id, -$valor);
            }
        }
        return false;
    }

    // Consultar saldo do cliente
    public function consultarSaldo($cliente_id) {
        $sql = "SELECT saldo FROM saldo_clientes WHERE cliente_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $cliente_id);
        $stmt->execute();
        $stmt->bind_result($saldo);
        $stmt->fetch();
        return $saldo ?? 0;
    }

    // Atualizar saldo do cliente
    private function atualizarSaldo($cliente_id, $valor) {
        $sql = "INSERT INTO saldo_clientes (cliente_id, saldo) VALUES (?, ?) ON DUPLICATE KEY UPDATE saldo = saldo + ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('idd', $cliente_id, $valor, $valor);
        return $stmt->execute();
    }
}
