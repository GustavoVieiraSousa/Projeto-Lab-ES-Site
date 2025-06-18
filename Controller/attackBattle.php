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

$moveType1P1 = $_SESSION['battle']['pokemon1']['atkType1'];
$moveType2P1 = $_SESSION['battle']['pokemon1']['atkType2'];
$moveType3P1 = $_SESSION['battle']['pokemon1']['atkType3'];
$moveType4P1 = $_SESSION['battle']['pokemon1']['atkType4'];

$pok1Slot1DamageRelations = $_SESSION['battle']['pokemon1']['damageRelations1'];
$pok1Slot2DamageRelations = $_SESSION['battle']['pokemon1']['damageRelations2'];

$moveClass1P1 = $_SESSION['battle']['pokemon1']['atkClass1'];
$moveClass2P1 = $_SESSION['battle']['pokemon1']['atkClass2'];
$moveClass3P1 = $_SESSION['battle']['pokemon1']['atkClass3'];
$moveClass4P1 = $_SESSION['battle']['pokemon1']['atkClass4'];

$pok1Slot1 = $_SESSION['battle']['pokemon1']['type1'];
$pok1Slot2 = $_SESSION['battle']['pokemon1']['type2'];

//pokemon 2
$pokPhysicalAttack2 = $_SESSION['battle']['pokemon2']['pokPhysicalAttack'];
$pokPhysicalDefense2 = $_SESSION['battle']['pokemon2']['pokPhysicalDefense'];
$pokSpecialAttack2 = $_SESSION['battle']['pokemon2']['pokSpecialAttack'];
$pokSpecialDefense2 = $_SESSION['battle']['pokemon2']['pokSpecialDefense'];

$moveType1P2 = $_SESSION['battle']['pokemon2']['atkType1'];
$moveType2P2 = $_SESSION['battle']['pokemon2']['atkType2'];
$moveType3P2 = $_SESSION['battle']['pokemon2']['atkType3'];
$moveType4P2 = $_SESSION['battle']['pokemon2']['atkType4'];

$pok2Slot1DamageRelations = $_SESSION['battle']['pokemon2']['damageRelations1'];
$pok2Slot2DamageRelations = $_SESSION['battle']['pokemon2']['damageRelations2'];

$moveClass1P2 = $_SESSION['battle']['pokemon2']['atkClass1'];
$moveClass2P2 = $_SESSION['battle']['pokemon2']['atkClass2'];
$moveClass3P2 = $_SESSION['battle']['pokemon2']['atkClass3'];
$moveClass4P2 = $_SESSION['battle']['pokemon2']['atkClass4'];

$pok2Slot1 = $_SESSION['battle']['pokemon2']['type1'];
$pok2Slot2 = $_SESSION['battle']['pokemon2']['type2'];

//set activePlayer
if($plaCode == $player1){ $activePlayer = "pokemon1";}
else if($plaCode == $player2){ $activePlayer = "pokemon2";}
else{ $activePlayer = NULL; }

// Descubra se o player é player1 ou player2
$isPlayer1 = ($plaCode == $player1);
$dmgField = $isPlayer1 ? 'batDmgCounterPlayer1' : 'batDmgCounterPlayer2';

// Defina o dano do ataque
if ($attack == '1') { $power = $_SESSION['battle'][$activePlayer]['power1']; $class = $_SESSION['battle'][$activePlayer]['atkClass1']; };
if ($attack == '2') { $power = $_SESSION['battle'][$activePlayer]['power2']; $class = $_SESSION['battle'][$activePlayer]['atkClass2']; };
if ($attack == '3') { $power = $_SESSION['battle'][$activePlayer]['power3']; $class = $_SESSION['battle'][$activePlayer]['atkClass3']; };
if ($attack == '4') { $power = $_SESSION['battle'][$activePlayer]['power4']; $class = $_SESSION['battle'][$activePlayer]['atkClass4']; };

srand(time());

//Set type elements default
$random = rand(217, 255)/255;
$STAB = 1; // Se o tipo do move for igual ao tipo do pokemon, seta $STAB para 1.5
$type1 = 1; //Multiplicador de Efetivo, aqui entra a tabela de multiplicadores de efetivo, setado pro primeiro tipo do pokemon adversario
$type2 = 1; //Multiplicador de Efetivo, aqui entra a tabela de multiplicadores de efetivo, setado pro segundo tipo do pokemon adversario
$level = 100;
$crit = rand(1,16) == 16 ? 2 : 1; // 6,25% de chance de crit

// Se passar de 255 tem que dividir por 4 e arredondar pra baixo (os caras sao loucos)
if($pokPhysicalAttack1 > 255 || $pokPhysicalDefense2 > 255 ){ $pokPhysicalAttack1 = floor($pokPhysicalAttack1 / 4); $pokPhysicalDefense2 = floor($pokPhysicalDefense2 / 4); }
if($pokPhysicalAttack2 > 255 || $pokPhysicalDefense1 > 255 ){ $pokPhysicalAttack2 = floor($pokPhysicalAttack2 / 4); $pokPhysicalDefense1 = floor($pokPhysicalDefense1 / 4); }
if($pokSpecialAttack1 > 255 || $pokSpecialDefense2 > 255 ){ $pokSpecialAttack1 = floor($pokSpecialAttack1 / 4); $pokSpecialDefense2 = floor($pokSpecialDefense2 / 4); }
if($pokSpecialAttack2 > 255 || $pokSpecialDefense1 > 255 ){ $pokSpecialAttack2 = floor($pokSpecialAttack2 / 4); $pokSpecialDefense1 = floor($pokSpecialDefense1 / 4); }

//set the actual battle elements for each player
if($plaCode == $player1){
    //typeCheck
    if($attack == '1'){ if($moveType1P1 == $pok1Slot1 || $moveType1P1 == $pok1Slot2){ $STAB = 1.5; }}
    if($attack == '2'){ if($moveType2P1 == $pok1Slot1 || $moveType2P1 == $pok1Slot2){ $STAB = 1.5; }}
    if($attack == '3'){ if($moveType3P1 == $pok1Slot1 || $moveType3P1 == $pok1Slot2){ $STAB = 1.5; }}
    if($attack == '4'){ if($moveType4P1 == $pok1Slot1 || $moveType4P1 == $pok1Slot2){ $STAB = 1.5; }}

    //EffectiveCheck
    if($attack == '1'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok2Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType1P1 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType1P1 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType1P1 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok2Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType1P1 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType1P1 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType1P1 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '2'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok2Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType2P1 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType2P1 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType2P1 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok2Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType2P1 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType2P1 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType2P1 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '3'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok2Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType3P1 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType3P1 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType3P1 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok2Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType3P1 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType3P1 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType3P1 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '4'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok2Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType4P1 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType4P1 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType4P1 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok2Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType4P1 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok2Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType4P1 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok2Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType4P1 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }

    //classCheck
    if($class == "physical"){ $elementMultiplier = $pokPhysicalAttack1 / $pokPhysicalDefense2; }
    if($class == "special"){ $elementMultiplier = $pokSpecialAttack1 / $pokSpecialDefense2; }
}
if($plaCode == $player2){
    if($attack == '1'){ if($moveType1P2 == $pok2Slot1 || $moveType1P2 == $pok2Slot2){ $STAB = 1.5; }}
    if($attack == '2'){ if($moveType2P2 == $pok2Slot1 || $moveType2P2 == $pok2Slot2){ $STAB = 1.5; }}
    if($attack == '3'){ if($moveType3P2 == $pok2Slot1 || $moveType3P2 == $pok2Slot2){ $STAB = 1.5; }}
    if($attack == '4'){ if($moveType4P2 == $pok2Slot1 || $moveType4P2 == $pok2Slot2){ $STAB = 1.5; }}

    //EffectiveCheck
    if($attack == '1'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok1Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType1P2 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType1P2 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType1P2 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok1Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType1P2 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType1P2 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType1P2 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '2'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok1Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType2P2 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType2P2 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType2P2 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok1Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType2P2 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType2P2 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot2DamageRelations['no_damage_from']['name'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType2P2 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '3'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok1Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType3P2 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType3P2 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType3P2 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok1Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType3P2 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType3P2 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType3P2 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }
    if($attack == '4'){
        //type 1
        $i = 0;
        $set1 = 0;
        $doubleDamageFrom = $pok1Slot1DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set1 == 0){
            if($moveType4P2 == $doubleDamageFrom[$i]){ $type1 = 2; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot1DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set1 == 0){
            if($moveType4P2 == $halfDamageFrom[$i]){ $type1 = 0.5; $set1 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot1DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set1 == 0){
            if($moveType4P2 == $noDamageFrom[$i]){ $type1 = 0; $set1 = 1; break; }
            $i++;
        }

        //type 2
        $i = 0;
        $set2 = 0;
        if($pok2Slot2 == NULL){ $type2 = 1; $set2 = 1; }
        $doubleDamageFrom = $pok1Slot2DamageRelations['double_damage_from'] ?? [];
        if (!is_array($doubleDamageFrom)){ $doubleDamageFrom = []; }
        while($i < sizeof($doubleDamageFrom) && $set2 == 0){
            if($moveType4P2 == $doubleDamageFrom[$i]){ $type2 = 2; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $halfDamageFrom = $pok1Slot2DamageRelations['half_damage_from'] ?? [];
        if (!is_array($halfDamageFrom)){ $halfDamageFrom = []; }
        while($i < sizeof($halfDamageFrom) && $set2 == 0){
            if($moveType4P2 == $halfDamageFrom[$i]){ $type2 = 0.5; $set2 = 1; break; }
            $i++;
        }
        $i = 0;
        $noDamageFrom = $pok1Slot2DamageRelations['no_damage_from'] ?? [];
        if (!is_array($noDamageFrom)){ $noDamageFrom = []; }
        while($i < sizeof($noDamageFrom) && $set2 == 0){
            if($moveType4P2 == $noDamageFrom[$i]){ $type2 = 0; $set2 = 1; break; }
            $i++;
        }
    }

    if($class == "physical"){ $elementMultiplier = $pokPhysicalAttack2 / $pokPhysicalDefense1; }
    if($class == "special"){ $elementMultiplier = $pokSpecialAttack2 / $pokSpecialDefense1; }
}

// Isso vai nos ataques
if($power != 0){ (int) $damage = ((((2*$level*$crit)/5 + 2)*$power*$elementMultiplier)/50 + 2)*$STAB*$type1*$type2*$random; }
else{ $damage = 0; }

try {
    $stmt = $conn->prepare("UPDATE battle SET $dmgField = ? WHERE batRooCode = ?");
    $stmt->execute([$damage, $roomCode]);
    echo json_encode(['success' => true, 'damage' => (int) $damage, 'attack' => $attack, 'crit' => $crit, 'elementMultiplier' => $elementMultiplier, 'type1' => $type1, 'type2' => $type2]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>