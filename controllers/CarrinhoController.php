<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Estoque.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'adicionar') {
    //echo "<pre>"; print_r($_POST); echo "</pre>"; exit;

    $produto_id = $_POST['produto_id'];
    $variacao = $_POST['variacao'];
    $quantidade = intval($_POST['quantidade']);

    if ($variacao === '' || !Estoque::verificarDisponibilidade($produto_id, $variacao, $quantidade)) {
        header("Location: ../views/carrinho.php?erro=estoque");
        exit;
    }

    adicionarAoCarrinho($produto_id, $variacao, $quantidade);

    header('Location: ../views/carrinho.php');
    exit;
}
