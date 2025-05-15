<?php
require_once __DIR__ . '/../config/database.php';

class Cupom {
    public static function criar($dados) {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO cupons (codigo, tipo, valor_desconto, minimo, teto_desconto, validade, usos_maximos)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
    
        return $stmt->execute([
            $dados['codigo'],
            $dados['tipo'],
            $dados['valor_desconto'],
            $dados['minimo'],
            $dados['teto_desconto'] ?: null,
            $dados['validade'],
            $dados['usos_maximos']
        ]);
    }
    
    // Verifica se o cupom existe e está válido
    public static function validar($codigo, $subtotal) {
        global $pdo;
        
        $codigo = strtoupper(trim($codigo));

        $stmt = $pdo->prepare("
            SELECT * 
            FROM cupons 
            WHERE UPPER(codigo) = ? AND validade >= CURDATE()");
        $stmt->execute([$codigo]);
        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$cupom) return null;
    
        // Verifica limite de uso
        if ($cupom['usos_maximos'] !== null && $cupom['usos_utilizados'] >= $cupom['usos_maximos']) {
            return null;
        }
    
        if ($subtotal < $cupom['minimo']) return null;
    
        $desconto = 0;
        if ($cupom['tipo'] === 'valor') {
            $desconto = $cupom['valor_desconto'];
        } elseif ($cupom['tipo'] === 'porcentagem') {
            $desconto = ($cupom['valor_desconto'] / 100) * $subtotal;
            if ($cupom['teto_desconto'] && $desconto > $cupom['teto_desconto']) {
                $desconto = $cupom['teto_desconto'];
            }
        }
    
        return [
            'codigo' => $cupom['codigo'],
            'desconto' => $desconto,
            'id' => $cupom['id'] // necessário para registrar o uso
        ];
    }

    public static function registrarUso($id) {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE cupons 
            SET usos_utilizados = usos_utilizados + 1 
            WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function listar() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT * 
            FROM cupons");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
