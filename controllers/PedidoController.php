<?php
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Produto.php';

require_once __DIR__ . '/../helpers.php';

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$carrinho = obterCarrinho();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'verificar_cupom') {
    require_once __DIR__ . '/../models/Cupom.php';

    $codigo = $_POST['codigo'] ?? '';
    $subtotal = floatval($_POST['subtotal'] ?? 0);

    $cupom = Cupom::validar($codigo, $subtotal);

    if (!$cupom) {
        echo json_encode(['erro' => 'Cupom inválido, expirado, já usado ou abaixo do valor mínimo.']);
        exit;
    }

    $frete = calcularFrete($subtotal);
    $total = max(0, $subtotal + $frete - $cupom['desconto']);

    echo json_encode([
        'mensagem' => "Cupom \"{$cupom['codigo']}\" aplicado!",
        'desconto' => number_format($cupom['desconto'], 2, ',', '.'),
        'total' => number_format($total, 2, ',', '.')
    ]);
    exit;
}

if (empty($carrinho)) {
    echo "<p>Carrinho vazio! Sessão não foi preenchida.</p>";
    print_r($_SESSION);
    exit;
}

foreach ($carrinho as $item) {

    $disponivel = Estoque::verificarDisponibilidade(
        $item['produto_id'],
        $item['variacao'],
        $item['quantidade']
    );

    if (!$disponivel) {
        header("Location: ../views/carrinho.php?erro=estoque");
        exit;
    }
}

$produtos_serializados = [];
// Calcula subtotal e frete
$subtotal = 0;
foreach ($carrinho as $item) {
    $produto = Produto::buscarPorId($item['produto_id']);
    if (!$produto) continue;

    $preco = $produto['preco'];
    $quantidade = $item['quantidade'];
    $subtotal += $produto['preco'] * $item['quantidade'];

    $produtos_serializados[] = "{$produto['nome']} ({$item['variacao']} x{$quantidade})";

    Estoque::baixarEstoque($item['produto_id'], $item['variacao'], $quantidade);
}
$frete = calcularFrete($subtotal);

// Cupom: Verifica se existe e é válido
//require_once __DIR__ . '/../models/Cupom.php';

$cupom_input = strtoupper(trim($_POST['cupom'] ?? ''));
$cupom_valido = null;
$desconto = 0;

if ($cupom_input) {
    $cupom_valido = Cupom::validar($cupom_input, $subtotal);
    if ($cupom_valido) {
        $desconto = $cupom_valido['desconto'];
        Cupom::registrarUso($cupom_valido['id']);
    }
}

// Total final com desconto
$total = $subtotal + $frete - $desconto;

$cep = $_POST['cep'];

// Captura e limpa os campos recebidos
$logradouro  = trim($_POST['endereco'] ?? '');
$numero      = trim($_POST['numero'] ?? '');
$complemento = trim($_POST['complemento'] ?? '');
$bairro      = trim($_POST['bairro'] ?? '');
$cidade      = trim($_POST['cidade'] ?? '');
$estado      = strtoupper(trim($_POST['estado'] ?? ''));

// Monta o complemento com vírgula, se existir complemento
$complemento_formatado = $complemento !== '' ? $complemento . ', ' : '';

// Monta o endereço final
$endereco_completo = "{$logradouro}, {$numero}, {$complemento_formatado}{$bairro}. {$cidade} - {$estado}";

$email_usuario = $_POST['email'];

$pedido_id = Pedido::criar(
    implode(', ', $produtos_serializados),
    $subtotal,
    $frete,
    $total,
    $cep,
    $endereco_completo,
    $email_usuario
);

// Envio de e-mail
enviarConfirmacaoPedido(
    $pedido_id,
    $email_usuario,
    $endereco_completo,
    $carrinho,
    $subtotal,
    $frete,
    $desconto,
    $total
);

limparCarrinho();

header("Location: ../views/confirmacao.php");
exit;
