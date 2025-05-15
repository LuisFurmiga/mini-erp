<?php
$rotas = [
    '/' => 'views/index.php',
    '/index' => 'views/index.php',
    '/finalizar' => 'controllers/PedidoController.php',
    '/estoque' => 'controllers/EstoqueController.php',
];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (array_key_exists($uri, $rotas)) {
    require __DIR__ . '/' . $rotas[$uri];
} else {
    http_response_code(404);
    echo "Página não encontrada";
}

