<?php
    require_once __DIR__ . '/../models/Cupom.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_cupom'])) {
        $dados = [
            'codigo' => $_POST['codigo'],
            'tipo' => $_POST['tipo'],
            'valor_desconto' => $_POST['valor_desconto'],
            'minimo' => $_POST['minimo'],
            'teto_desconto' => $_POST['teto_desconto'],
            'validade' => $_POST['validade'],
            'usos_maximos' => $_POST['usos_maximos'],
        ];
    
        if (Cupom::criar($dados)) {
            header("Location: cupons.php"); // redireciona para evitar reenvio
            exit;
        } else {
            echo "<p style='color:red;'>❌ Erro ao criar o cupom.</p>";
        }
    }
    
    $cupons = Cupom::listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Cupons</title>
    <link rel="stylesheet" href="../public/css/cupons.css">
</head>

<body>
    <h2>Criar Novo Cupom</h2>
    <form method="post" style="margin-bottom: 30px;">
        <input type="hidden" name="criar_cupom" value="1">

        <label>Código:
            <input type="text" name="codigo" required>
        </label>

        <label>Tipo:
            <select name="tipo" required>
                <option value="valor">Valor Fixo</option>
                <option value="porcentagem">Porcentagem</option>
            </select>
        </label>

        <label>Desconto:
            <input type="number" step="0.01" name="valor_desconto" required>
        </label>

        <label>Valor Mínimo:
            <input type="number" step="0.01" name="minimo" value="0">
        </label>

        <label>Teto de Desconto (%):
            <input type="number" step="0.01" name="teto_desconto">
        </label>

        <label>Validade:
            <input type="date" name="validade" required>
        </label>

        <label>Usos Máximos:
            <input type="number" name="usos_maximos" required>
        </label>

        <button type="submit">➕ Criar Cupom</button>
    </form>

    <h1>Cupons Cadastrados</h1>
    <table>
        <caption>Resumo de Cupons Ativos</caption>
        <tr>
            <th>Código</th>
            <th>Tipo</th>
            <th>Desconto</th>
            <th>Teto (se %)</th>
            <th>Valor Mínimo</th>
            <th>Usos</th>
            <th>Validade</th>
            <th>Criado em</th>
        </tr>
        <?php foreach ($cupons as $cupom): ?>
            <tr>
                <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                <td><?= ucfirst($cupom['tipo']) ?></td>
                <td>
                    <?= $cupom['tipo'] === 'porcentagem'
                        ? $cupom['valor_desconto'] . '%'
                        : 'R$' . number_format($cupom['valor_desconto'], 2, ',', '.') ?>
                </td>
                <td>
                    <?= $cupom['teto_desconto'] !== null
                        ? 'R$' . number_format($cupom['teto_desconto'], 2, ',', '.')
                        : '-' ?>
                </td>
                <td>R$<?= number_format($cupom['minimo'], 2, ',', '.') ?></td>
                <td><?= $cupom['usos_utilizados'] ?>/<?= $cupom['usos_maximos'] ?></td>
                <td><?= date('d/m/Y', strtotime($cupom['validade'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($cupom['criado_em'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="index.php">⬅️ Voltar para a loja</a></p>
</body>

</html>
