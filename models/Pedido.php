<?php
require_once __DIR__ . '/../config/database.php';

class Pedido {
    public static function criar($produtos, $subtotal, $frete, $total, $cep, $endereco, $email) {
        global $pdo;

        $stmt = $pdo->prepare("
            INSERT INTO pedidos (produtos, subtotal, frete, total, cep, endereco, email_cliente)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $produtos,
            $subtotal,
            $frete,
            $total,
            $cep,
            $endereco,
            $email
        ]);

        // Retorna o ID do pedido criado
        return $pdo->lastInsertId();
    }

    public static function listar() {
        global $pdo;

        $stmt = $pdo->query("
            SELECT * 
            FROM pedidos 
            ORDER BY criado_em DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function atualizarStatus($id, $status) {
        global $pdo;

        $stmt = $pdo->prepare("
            UPDATE pedidos 
            SET status = ? 
            WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function deletar($id) {
        global $pdo;

        $stmt = $pdo->prepare("
            DELETE FROM pedidos 
            WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function buscar($id) {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT * 
            FROM pedidos 
            WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
