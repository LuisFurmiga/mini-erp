<?php
require_once __DIR__ . '/../models/Cupom.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $valor_desconto = $_POST['valor_desconto'];
    $minimo = $_POST['minimo'];
    $validade = $_POST['validade'];

    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO cupons (codigo, valor_desconto, minimo, validade) 
        VALUES (?, ?, ?, ?)");
    $stmt->execute([$codigo, $valor_desconto, $minimo, $validade]);

    header('Location: ../views/index.php');
    exit;
}
