<?php
//==========================================================
// Recebe um JSON com o codigo do time do jogador e valida
//==========================================================

session_start();

require_once 'connection.php';

header('Content-Type: application/json');

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['error' => 'POST nao encontrado.']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['teamId'])) {
    $teamId = $data['teamId'];
}

if (!$teamId) {
    echo json_encode(['error' => 'ID do time não fornecido.']);
    exit();
}

echo json_encode(['success' => true, 'message' => 'Time encontrado.']);
exit();
?>