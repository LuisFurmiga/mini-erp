<?php
    require_once __DIR__ . '/../helpers.php';
    require_once __DIR__ . '/../models/Produto.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['erro'])) {
        echo "<p style='color: red;'><strong>❌ {$_SESSION['erro']}</strong></p>";
        unset($_SESSION['erro']);
    }
    
    if (isset($_SESSION['info'])) {
        echo "<p style='color: blue;'><strong>ℹ️ {$_SESSION['info']}</strong></p>";
        unset($_SESSION['info']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $produto_id = $_POST['produto_id'];
        $variacao = $_POST['variacao'];
        $chave = $produto_id . '_' . $variacao;
        $acao = $_POST['acao'];
        $quantidade = intval($_POST['quantidade'] ?? 1);
    
        if (isset($_SESSION['carrinho'][$chave])) {
            switch ($acao) {
                case 'diminuir':
                    $_SESSION['carrinho'][$chave]['quantidade'] = max(1, $_SESSION['carrinho'][$chave]['quantidade'] - 1);
                    break;
                case 'aumentar':
                    require_once __DIR__ . '/../models/Estoque.php';
                    $estoque = Estoque::buscarPorProdutoEVariação($produto_id, $variacao);

                    if ($estoque && $_SESSION['carrinho'][$chave]['quantidade'] < $estoque['quantidade']) {
                        $_SESSION['carrinho'][$chave]['quantidade'] += 1;
                    } else {
                        $_SESSION['erro'] = 'Estoque insuficiente para aumentar a quantidade.';
                    }
                    break;
                case 'atualizar':
                    require_once __DIR__ . '/../models/Estoque.php';
                    $estoque = Estoque::buscarPorProdutoEVariação($produto_id, $variacao);

                    if ($estoque) {
                        $novaQtd = min($quantidade, $estoque['quantidade']);
                        $_SESSION['carrinho'][$chave]['quantidade'] = max(1, $novaQtd);
                        if ($quantidade > $estoque['quantidade']) {
                            $_SESSION['info'] = "A quantidade foi ajustada para o limite disponível de {$estoque['quantidade']}.";
                        }                        
                    }                    
                    break;
            }
        }
    
        header("Location: carrinho.php");
        exit;
    }

    if (isset($_GET['remover']) && isset($_GET['variacao'])) {
        $produto_id = $_GET['remover'];
        $variacao = $_GET['variacao'];
        $chave = $produto_id . '_' . $variacao;

        if (isset($_SESSION['carrinho'][$chave])) {
            unset($_SESSION['carrinho'][$chave]);
        }

        header("Location: carrinho.php");
        exit;
    }

    if (isset($_GET['limpar']) && $_GET['limpar'] == 1) {
        limparCarrinho();
        header("Location: carrinho.php");
        exit;
    }

    $carrinho = obterCarrinho();
?>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'estoque'): ?>
    <p style="color: red;">❌ Não há estoque suficiente para um dos produtos.</p>
<?php endif; ?>

<h1>Meu Carrinho</h1>

<?php if (empty($carrinho)): ?>
    <p>Seu carrinho está vazio.</p>
    <a href="index.php">Adicionar produtos</a>
<?php else: ?>

    <ul>
        <?php
            $subtotal = 0;

            foreach ($carrinho as $item):
                $produto = Produto::buscarPorId($item['produto_id']);
                if (!$produto) continue;
                $preco = $produto['preco'];
                $quantidade = $item['quantidade'];
                $total_item = $preco * $quantidade;
                $subtotal += $total_item;
        ?>
        <li>
            <form method="post" action="carrinho.php" style="display: flex; align-items: center; gap: 10px;">
                <input type="hidden" name="produto_id" value="<?= $item['produto_id'] ?>">
                <input type="hidden" name="variacao" value="<?= htmlspecialchars($item['variacao']) ?>">

                <?= htmlspecialchars($produto['nome']) ?> —
                Variação: <?= htmlspecialchars($item['variacao']) ?> —

                <button type="submit" name="acao" value="diminuir">➖</button>

                <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" style="width: 50px; text-align: center;">

                <button type="submit" name="acao" value="aumentar">➕</button>

                <button type="submit" name="acao" value="atualizar">✅ Atualizar</button>

                <strong>R$<?= number_format($total_item, 2, ',', '.') ?></strong>

                <a href="carrinho.php?remover=<?= $item['produto_id'] ?>&variacao=<?= urlencode($item['variacao']) ?>" style="color: red;">🗑️</a>
            </form>
        </li>


        <?php endforeach; ?>
        <p>
            <a href="carrinho.php?limpar=1" style="color: red;">❌ Limpar Carrinho</a>
        </p>
    </ul>

    <p>
        <strong>Subtotal:</strong> R$<?= number_format($subtotal, 2, ',', '.') ?>
    </p>
    <p>
        <strong>Frete estimado:</strong> R$<?= number_format(calcularFrete($subtotal), 2, ',', '.') ?>
    </p>
    <p>
        <a href="finalizar.php" style="display: inline-block; padding: 10px 15px; background-color:rgb(5, 5, 5); color: white; text-decoration: none; border-radius: 5px;">
            🔥 Finalizar Pedido
        </a>
    </p>

    <p>
        <a href="index.php" style="display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            🛒 Continuar Comprando
        </a>
    </p>

<?php endif; ?>
