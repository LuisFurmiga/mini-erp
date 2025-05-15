<?php
    require_once __DIR__ . '/../config/database.php';

class Produto {
    public static function listar() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT * 
            FROM produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function salvar($nome, $preco) {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO produtos (nome, preco) 
            VALUES (?, ?)");
        $stmt->execute([$nome, $preco]);
        return $pdo->lastInsertId();
    }

    public static function buscarPorId($id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * 
            FROM produtos 
            WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function excluir($id) {
        global $pdo;
        $stmt = $pdo->prepare("
            DELETE FROM produtos 
            WHERE id = ?");
        return $stmt->execute([$id]);
    }    
    
}
