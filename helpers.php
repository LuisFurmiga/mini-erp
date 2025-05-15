<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function adicionarAoCarrinho($produto_id, $variacao, $quantidade) {
    $_SESSION['carrinho'] ??= [];

    $chave = $produto_id . '_' . $variacao;
    if (!isset($_SESSION['carrinho'][$chave])) {
        $_SESSION['carrinho'][$chave] = [
            'produto_id' => $produto_id,
            'variacao' => $variacao,
            'quantidade' => $quantidade
        ];
    } else {
        $_SESSION['carrinho'][$chave]['quantidade'] += $quantidade;
    }
}

function obterCarrinho() {
    return $_SESSION['carrinho'] ?? [];
}

function limparCarrinho() {
    unset($_SESSION['carrinho']);
}

function calcularFrete($subtotal) {
    if ($subtotal > 200) return 0;
    if ($subtotal >= 52 && $subtotal <= 166.59) return 15;
    return 20;
}

function enviarConfirmacaoPedido($pedido_id, $email_usuario, $endereco, $carrinho, $subtotal, $frete, $desconto, $total) {
    require_once __DIR__ . '/models/Produto.php';
    require_once __DIR__ . '/email.php';

    // Monta lista de produtos
    $linha_produtos = "";
    foreach ($carrinho as $item) {
        $produto = Produto::buscarPorId($item['produto_id']);
        if (!$produto) continue;

        $nome = htmlspecialchars($produto['nome']);
        $variacao = htmlspecialchars($item['variacao']);
        $quantidade = $item['quantidade'];
        $valor_total = $produto['preco'] * $quantidade;

        $linha_produtos .= "$nome — Variação: $variacao — Quantidade: $quantidade — Total: R$" .
                           number_format($valor_total, 2, ',', '.') . "\n";
    }

    // Monta o corpo do e-mail
    $mensagem = "✅ Seu pedido foi registrado com sucesso!\n\n";
    $mensagem .= "🆔 Código do pedido: #{$pedido_id}\n\n";
    $mensagem .= "📦 Itens do pedido:\n$linha_produtos\n";
    $mensagem .= "📍 Endereço de entrega:\n$endereco\n\n";
    $mensagem .= "💰 Subtotal: R$" . number_format($subtotal, 2, ',', '.') . "\n";
    $mensagem .= "🚚 Frete: R$" . number_format($frete, 2, ',', '.') . "\n";
    if ($desconto > 0) {
        $mensagem .= "🏷️ Desconto: -R$" . number_format($desconto, 2, ',', '.') . "\n";
    }
    $mensagem .= "🧾 Total: R$" . number_format($total, 2, ',', '.') . "\n\n";
    $mensagem .= "Obrigado por comprar com a gente! 🎉";

    // ✅ Log do e-mail enviado (para debug ou histórico)
    $logsPath = __DIR__ . '../logs';
    if (!is_dir($logsPath)) {
        @mkdir($logsPath, 0755, true);
    }

    $timestamp = date('Ymd_Hi');
    $arquivo_log = "{$logsPath}/pedido-{$pedido_id}_{$timestamp}.txt";

    file_put_contents($arquivo_log, $mensagem);

    // Envia o e-mail
    enviarEmail($email_usuario, 'Pedido confirmado - Mini ERP', $mensagem);
}
