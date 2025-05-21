<?php
header('Content-Type: application/json');
session_start();
require_once("connection.php");

$data = json_decode(file_get_contents('php://input'), true);
$attack = $data['attack'] ?? null;

if (!isset($_SESSION['roomCode'], $_SESSION['plaCode'], $_SESSION['battle'])) {
    echo json_encode(['success' => false, 'error' => 'Sessão inválida']);
    exit();
}

$roomCode = $_SESSION['roomCode'];
$plaCode = $_SESSION['plaCode'];
$battle = $_SESSION['battle'];

// Descubra se o player é player1 ou player2
$isPlayer1 = ($plaCode == $battle['player1']);
$dmgField = $isPlayer1 ? 'batDmgCounterPlayer1' : 'batDmgCounterPlayer2';

// Defina o dano do ataque (exemplo simples, personalize conforme sua lógica)
$damage = 20;
if ($attack == 'thunder') $damage = 40;
if ($attack == 'quick_attack') $damage = 15;
if ($attack == 'double_team') $damage = 0; // Exemplo: não causa dano

try {
    $stmt = $conn->prepare("UPDATE battle SET $dmgField = ? WHERE batRooCode = ?");
    $stmt->execute([$damage, $roomCode]);
    echo json_encode(['success' => true, 'damage' => $damage]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>