<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Estoque.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $variacao = $_POST['variacao'] ?? '';
    $quantidade = $_POST['quantidade'] ?? 0;

    if ($nome && $preco > 0 && $quantidade > 0 && $variacao !== '') {
        $produto_id = Produto::salvar($nome, $preco);
        Estoque::adicionar($produto_id, $variacao, $quantidade);
        header('Location: ../views/index.php');
        exit;
    } else {
        header('Location: ../views/index.php?erro=preco');
        exit;
    }
    
}
