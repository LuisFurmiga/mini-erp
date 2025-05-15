<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
        require_once __DIR__ . '/../models/Produto.php';
        Produto::excluir(intval($_POST['excluir']));
        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/../controllers/ProdutoController.php';
    }

    require_once __DIR__ . '/../models/Produto.php';
    require_once __DIR__ . '/../models/Estoque.php';

    // Agrupar produtos por nome
    $produtos_raw = Produto::listar();
    $agrupados = [];

    foreach ($produtos_raw as $produto) {
        $estoques = Estoque::listarPorProduto($produto['id']);

        foreach ($estoques as $e) {
            $nome = $produto['nome'];
            $preco = floatval($produto['preco']);

            if (!isset($agrupados[$nome])) {
                $agrupados[$nome] = [
                    'menor_preco' => $preco,
                    'maior_preco' => $preco,
                    'variacoes' => []
                ];
            } else {
                $agrupados[$nome]['menor_preco'] = min($agrupados[$nome]['menor_preco'], $preco);
                $agrupados[$nome]['maior_preco'] = max($agrupados[$nome]['maior_preco'], $preco);
            }

            $agrupados[$nome]['variacoes'][] = [
                'produto_id' => $produto['id'],
                'variacao' => $e['variacao'],
                'quantidade' => $e['quantidade'],
                'preco' => $preco
            ];
        }
    }

?>


<p><a href="carrinho.php">🛒 Ver Carrinho</a></p>

<h1>Produtos</h1>
<ul>
<?php foreach ($agrupados as $nome => $dados): ?>
    <div class="produto">
        <p class="titulo-produto" style="font-weight: bold;">
            <?= htmlspecialchars($nome) ?> —
            <span class="preco-dinamico">
                R$<?= number_format($dados['menor_preco'], 2, ',', '.') ?> - R$<?= number_format($dados['maior_preco'], 2, ',', '.') ?>
            </span>
        </p>

        <form method="post" action="../controllers/CarrinhoController.php" style="margin-top: 10px;">
            <input type="hidden" name="produto_id" class="produto-id">

            <label>Variação:
                <select name="variacao" required class="select-variacao" data-produto="<?= $nome ?>">
                    <option value="">Selecione</option>
                    <?php foreach ($dados['variacoes'] as $v): ?>
                        <option value="<?= $v['variacao'] ?>"
                                data-produto-id="<?= $v['produto_id'] ?>"
                                data-qtd="<?= $v['quantidade'] ?>"
                                data-preco="<?= $v['preco'] ?>">
                            <?= $v['variacao'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <span class="qtd-disponivel" style="margin-left: 10px; font-size: 0.9em;"></span><br><br>

            <p class="preco-selecionado" style="font-weight: bold;"></p>

            <label>Quantidade:
                <input type="number" name="quantidade" value="1" min="1" required>
            </label>

            <button type="submit" name="acao" value="adicionar">Adicionar ao Carrinho</button>
        </form>
    </div>
    <hr>
<?php endforeach; ?>

<script>
document.querySelectorAll('.select-variacao').forEach(select => {
    select.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const produtoId = selected.getAttribute('data-produto-id');
        const qtd = selected.getAttribute('data-qtd');
        const preco = selected.getAttribute('data-preco');

        const form = this.closest('form');
        const hiddenInput = form.querySelector('.produto-id');
        const spanQtd = form.querySelector('.qtd-disponivel');

        if (hiddenInput) hiddenInput.value = produtoId;
        if (spanQtd) spanQtd.innerHTML = `🟢 ${qtd} disponível`;

        // Atualiza o preço no título
        const titulo = form.previousElementSibling;
        const precoSpan = titulo.querySelector('.preco-dinamico');
        if (precoSpan) precoSpan.innerText = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;

        // Remove a opção "Selecione"
        const primeiraOpcao = this.querySelector('option[value=""]');
        if (primeiraOpcao) primeiraOpcao.remove();
    });
});
</script>

</ul>

<h2>Cadastrar novo produto</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="nome" placeholder="Nome do Produto" required>
    <input type="number" name="preco" step="0.01" min="0.01" required placeholder="Preço">
    <input type="text" name="variacao" placeholder="Variação (ex: 500g)" required>
    <input type="number" name="quantidade" min="1" placeholder="Quantidade em Estoque" required>
    <button type="submit">Salvar</button>
</form>

