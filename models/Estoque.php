<?php
require_once __DIR__ . '/../config/database.php';

class Estoque {
    public static function listarPorProduto($produto_id) {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT * 
            FROM estoque 
            WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function adicionar($produto_id, $variacao, $quantidade) {
        global $pdo;

        $stmt = $pdo->prepare("
            INSERT INTO estoque (produto_id, variacao, quantidade)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$produto_id, $variacao, $quantidade]);
    }

    public static function atualizar($id, $quantidade) {
        global $pdo;

        $stmt = $pdo->prepare("
            UPDATE estoque 
            SET quantidade = ? 
            WHERE id = ?");
        return $stmt->execute([$quantidade, $id]);
    }

    public static function baixarEstoque($produto_id, $variacao, $quantidade) {
        global $pdo;

        $stmt = $pdo->prepare("
            UPDATE estoque
            SET quantidade = quantidade - ?
            WHERE produto_id = ? AND variacao = ? AND quantidade >= ?
        ");

        return $stmt->execute([$quantidade, $produto_id, $variacao, $quantidade]);
    }

    public static function buscarPorProdutoEVariação($produtoId, $variacao) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * 
            FROM estoque 
            WHERE produto_id = ? AND variacao = ?");
        $stmt->execute([$produtoId, $variacao]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public static function verificarDisponibilidade($produto_id, $variacao, $quantidade) {
        global $pdo;
    
        $stmt = $pdo->prepare("
            SELECT quantidade FROM estoque
            WHERE produto_id = ? AND variacao = ?
        ");
        $stmt->execute([$produto_id, $variacao]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result && $result['quantidade'] >= $quantidade;
    }
    
}
