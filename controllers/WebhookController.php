<?php
require_once __DIR__ . '/../models/Pedido.php';
header('Content-Type: application/json');

// Lê dados JSON enviados via POST
$input = json_decode(file_get_contents('php://input'), true);

// Validação básica
if (!isset($input['id'], $input['status'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID e status são obrigatórios']);
    exit;
}

$id = (int) $input['id'];
$status = strtolower(trim($input['status']));

// Executa a ação com base no status
if ($status === 'cancelado') {
    $removido = Pedido::deletar($id);
    echo json_encode([
        'mensagem' => $removido ? 'Pedido cancelado e removido.' : 'Pedido não encontrado ou já removido.'
    ]);
} else {
    $atualizado = Pedido::atualizarStatus($id, $status);
    echo json_encode([
        'mensagem' => $atualizado ? 'Status do pedido atualizado.' : 'Erro ao atualizar pedido.'
    ]);
}
