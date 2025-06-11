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

//pokemon 1
$pokPhysicalAttack1 = $_SESSION['battle']['pokemon1']['pokPhysicalAttack'];
$pokPhysicalDefense1 = $_SESSION['battle']['pokemon1']['pokPhysicalDefense'];
$pokSpecialAttack1 = $_SESSION['battle']['pokemon1']['pokSpecialAttack'];
$pokSpecialDefense1 = $_SESSION['battle']['pokemon1']['pokSpecialDefense'];

$moveClass1P1 = $_SESSION['battle']['pokemon1']['atkClass1'];
$moveClass2P1 = $_SESSION['battle']['pokemon1']['atkClass2'];
$moveClass3P1 = $_SESSION['battle']['pokemon1']['atkClass3'];
$moveClass4P1 = $_SESSION['battle']['pokemon1']['atkClass4'];

//pokemon 2
$pokPhysicalAttack2 = $_SESSION['battle']['pokemon2']['pokPhysicalAttack'];
$pokPhysicalDefense2 = $_SESSION['battle']['pokemon2']['pokPhysicalDefense'];
$pokSpecialAttack2 = $_SESSION['battle']['pokemon2']['pokSpecialAttack'];
$pokSpecialDefense2 = $_SESSION['battle']['pokemon2']['pokSpecialDefense'];

$moveClass1P2 = $_SESSION['battle']['pokemon2']['atkClass1'];
$moveClass2P2 = $_SESSION['battle']['pokemon2']['atkClass2'];
$moveClass3P2 = $_SESSION['battle']['pokemon2']['atkClass3'];
$moveClass4P2 = $_SESSION['battle']['pokemon2']['atkClass4'];

//set activePlayer
if($plaCode == $player1){ $activePlayer = "pokemon1";}
else if($plaCode == $player2){ $activePlayer = "pokemon2";}
else{ $activePlayer = NULL; }

// Descubra se o player é player1 ou player2
$isPlayer1 = ($plaCode == $player1);
$dmgField = $isPlayer1 ? 'batDmgCounterPlayer1' : 'batDmgCounterPlayer2';

// Defina o dano do ataque (exemplo simples, personalize conforme sua lógica)
if ($attack == '1') { $power = $_SESSION['battle'][$activePlayer]['power1']; $class = $_SESSION['battle'][$activePlayer]['atkClass1']; };
if ($attack == '2') { $power = $_SESSION['battle'][$activePlayer]['power2']; $class = $_SESSION['battle'][$activePlayer]['atkClass2']; };
if ($attack == '3') { $power = $_SESSION['battle'][$activePlayer]['power3']; $class = $_SESSION['battle'][$activePlayer]['atkClass3']; };
if ($attack == '4') { $power = $_SESSION['battle'][$activePlayer]['power4']; $class = $_SESSION['battle'][$activePlayer]['atkClass4']; };

srand(time());

$level = 100;
$crit = rand(1,16) == 16 ? 2 : 1; // 6,25% de chance de crit

// Se passar de 255 tem que dividir por 4 e arredondar pra baixo (os caras sao loucos)
if($pokPhysicalAttack1 > 255 || $pokPhysicalDefense2 > 255 ){ $pokPhysicalAttack1 = floor($pokPhysicalAttack1 / 4); $pokPhysicalDefense2 = floor($pokPhysicalDefense2 / 4); }
if($pokPhysicalAttack2 > 255 || $pokPhysicalDefense1 > 255 ){ $pokPhysicalAttack2 = floor($pokPhysicalAttack2 / 4); $pokPhysicalDefense1 = floor($pokPhysicalDefense1 / 4); }
if($pokSpecialAttack1 > 255 || $pokSpecialDefense2 > 255 ){ $pokSpecialAttack1 = floor($pokSpecialAttack1 / 4); $pokSpecialDefense2 = floor($pokSpecialDefense2 / 4); }
if($pokSpecialAttack2 > 255 || $pokSpecialDefense1 > 255 ){ $pokSpecialAttack2 = floor($pokSpecialAttack2 / 4); $pokSpecialDefense1 = floor($pokSpecialDefense1 / 4); }

//set $elementMultiplier
if($plaCode == $player1){
    if($class == "physical"){ $elementMultiplier = $pokPhysicalAttack1 / $pokPhysicalDefense2; }
    if($class == "special"){ $elementMultiplier = $pokSpecialAttack1 / $pokSpecialDefense2; }
}
if($plaCode == $player2){
    if($class == "physical"){ $elementMultiplier = $pokPhysicalAttack2 / $pokPhysicalDefense1; }
    if($class == "special"){ $elementMultiplier = $pokSpecialAttack2 / $pokSpecialDefense1; }
}

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
    echo json_encode(['success' => true, 'damage' => (int) $damage, 'attack' => $attack, 'crit' => $crit, 'elementMultiplier' => $elementMultiplier]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>