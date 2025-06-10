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
$player1 = $_SESSION['battle']['player1'];
$player2 = $_SESSION['battle']['player2'];

//set activePlayer
if($plaCode == $player1){ $activePlayer = "pokemon1"; }
else if($plaCode == $player2){ $activePlayer = "pokemon2"; }
else{ $activePlayer = NULL; }



// Descubra se o player é player1 ou player2
$isPlayer1 = ($plaCode == $player1);
$dmgField = $isPlayer1 ? 'batDmgCounterPlayer1' : 'batDmgCounterPlayer2';

// Defina o dano do ataque (exemplo simples, personalize conforme sua lógica)
if ($attack == '1') { $power = $_SESSION['battle'][$activePlayer]['power1']; };
if ($attack == '2') { $power = $_SESSION['battle'][$activePlayer]['power2']; };
if ($attack == '3') { $power = $_SESSION['battle'][$activePlayer]['power3']; };
if ($attack == '4') { $power = $_SESSION['battle'][$activePlayer]['power4']; };

srand(time());

$level = 100;
$crit = rand(1,10) == 10 ? 2 : 1; // 10% de chance de crit
$elementMultiplier = 1; //Special Attack ou Normal Attack, nao vou mexer com isso pois nao sei se tenho esses dados, ent deixa ai
$random = rand(217, 255)/255;
$STAB = 1; //TODO: Multiplicador se o tipo do ataque é o mesmo do tipo do pokemon, se SIM = 1.5x, se NAO = 1;
$type1 = 1; //TODO: Multiplicador de Efetivo, aqui entra a tabela de multiplicadores de efetivo, setado pro primeiro tipo do pokemon adversario
$type2 = 1; //TODO: Multiplicador de Efetivo, aqui entra a tabela de multiplicadores de efetivo, setado pro segundo tipo do pokemon adversario

// Isso vai nos ataques
if($power != 0){ (int) $damage = ((((2*$level*$crit)/5 + 2)*$power*$elementMultiplier)/50 + 2)*$STAB*$type1*$type2*$random; }
else{ $damage = 0; }

try {
    $stmt = $conn->prepare("UPDATE battle SET $dmgField = ? WHERE batRooCode = ?");
    $stmt->execute([$damage, $roomCode]);
    echo json_encode(['success' => true, 'damage' => (int) $damage, 'attack' => $attack, 'crit' => $crit]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>