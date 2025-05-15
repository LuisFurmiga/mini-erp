<?php 
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Cupom.php';

$carrinho = obterCarrinho();

$subtotal = 0;
foreach ($carrinho as $item) {
    $produto = Produto::buscarPorId($item['produto_id']);
    if (!$produto) continue;

    $subtotal += $produto['preco'] * $item['quantidade'];
}

$frete = calcularFrete($subtotal);
$total = $subtotal + $frete;

// Verifica cupom, se enviado via GET
$desconto = 0;
$cupom_input = $_GET['cupom'] ?? '';
$cupom_valido = null;

if ($cupom_input) {
    $cupom_valido = Cupom::validar($cupom_input, $subtotal);
    if ($cupom_valido) {
        $desconto = $cupom_valido['desconto'];
        $total -= $desconto;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pedido</title>
</head>
<body>
    <h2>Resumo do Pedido</h2>
    <ul>
        <li><strong>Subtotal:</strong> R$<span id="subtotal"><?= number_format($subtotal, 2, ',', '.') ?></span></li>
        <li><strong>Frete:</strong> R$<span id="frete"><?= number_format($frete, 2, ',', '.') ?></span></li>
        <li><strong>Desconto:</strong> R$<span id="desconto">0,00</span></li>
        <li><strong>Total a pagar:</strong> R$<span id="total"><?= number_format($total, 2, ',', '.') ?></span></li>
    </ul>

    <h1>Finalizar Pedido</h1>

    <form method="post" action="../controllers/PedidoController.php">
        <label for="cep">CEP:</label>
        <input type="text" name="cep" id="cep" onblur="buscarCEP(this.value)" required><br><br>

        <label>Endereço:
            <input type="text" name="endereco" id="endereco" required>
        </label><br><br>

        <label>Número:
            <input type="text" name="numero" id="numero" required>
        </label><br><br>

        <label>Complemento:
            <input type="text" name="complemento" id="complemento">
        </label><br><br>

        <label>Bairro:
            <input type="text" name="bairro" id="bairro" required>
        </label><br><br>

        <label>Cidade:
            <input type="text" name="cidade" id="cidade" required>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" id="estado" required>
        </label><br><br>

        <label for="email">Seu e-mail:</label>
        <input type="email" name="email" required><br><br>

        <label for="cupom">Cupom (opcional):</label>
        <input type="text" name="cupom" id="cupom" style="text-transform: uppercase;">
        <button type="button" onclick="aplicarCupom()">Aplicar</button>
        <br><br>
        <div id="mensagem-cupom" style="color: green; font-weight: bold;"></div><br><br>

        <button type="submit">Finalizar Pedido</button>
        <p><a href="index.php">🛒 Continuar Comprando</a></p>
    </form>

    <script src="../public/js/cep.js"></script>

    <script>
        function aplicarCupom() {
            const codigo = document.getElementById('cupom').value.trim();
            const subtotal = parseFloat(document.getElementById('subtotal').innerText.replace(',', '.'));
            const frete = parseFloat(document.getElementById('frete').innerText.replace(',', '.'));

            fetch('../controllers/PedidoController.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `acao=verificar_cupom&codigo=${encodeURIComponent(codigo)}&subtotal=${subtotal}`
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('mensagem-cupom');
                if (data.erro) {
                    msg.style.color = 'red';
                    msg.innerText = data.erro;
                    document.getElementById('desconto').innerText = '0,00';
                    document.getElementById('total').innerText = (subtotal + frete).toFixed(2).replace('.', ',');
                } else {
                    msg.style.color = 'green';
                    msg.innerText = data.mensagem;
                    document.getElementById('desconto').innerText = data.desconto;
                    document.getElementById('total').innerText = data.total;
                }
            })
            .catch(() => {
                document.getElementById('mensagem-cupom').innerText = 'Erro ao verificar o cupom.';
            });
        }
    </script>

</body>
</html>
