<?php
//===========================================================================
// Recebe o codigo do time do jogador e envia para a battle.php
//===========================================================================

require_once 'connection.php';
session_start();

// Barra se nao vier de lobbyToBattleTeam.js
if (!$_SERVER['REQUEST_METHOD'] === 'POST') { 
    $_SESSION['message'] = "Método inválido.";
    exit();
}
// Verifica se o jogador está logado
if(!isset($_SESSION['plaCode'])) { 
    $_SESSION['message'] = "Nenhum jogador logado.";
    exit();
}
// Verifica se a sala está instanciada na sessao
if(!isset($_SESSION['roomCode'])) {
    $_SESSION['message'] = "Nenhum código de sala encontrado.";
    exit();
}
// Verifica se o time foi enviado
if (!isset($_POST['teamId'])) {
    $_SESSION['message'] = "Nenhum Team ID foi enviado.";
    exit();
}

$teamId = $_POST['teamId'];
$plaCode = $_SESSION['plaCode'];
$roomCode = $_SESSION['roomCode'];

// sala ainda ativa
try{
    $existsRoomStmt = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
    $existsRoomStmt->execute([$roomCode]);
    $existsRoom = $existsRoomStmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e){
    error_log('Erro ao criar Sala: ' . $e->getMessage());
    $_SESSION['message'] = 'Falha ao buscar sala: ' . $e->getMessage();
    exit();
}
if($existsRoom == null){
    $_SESSION['message'] = 'Sala não existe.';
    unset($_SESSION['roomCode']);
    header("Location: ../View/roomList.php");
    exit();
}

// Pesquisa quem é o dono da sala (tratamento)
try{
    $ownerRoom = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
    $ownerRoom->execute([$roomCode]);
    $ownerRoom = $ownerRoom->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    $_SESSION['message'] = "Erro ao buscar dono da sala: " . $e->getMessage();
    exit();
}

// pesquisa se existem batalhas ja criadas
try{
    $battleCheckStmt = $conn->prepare("SELECT * FROM battle WHERE batRooCode = ?");
    $battleCheckStmt->execute([$roomCode]);
    $battleCheck = $battleCheckStmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    $_SESSION['message'] = "Erro ao buscar batalhas: " . $e->getMessage();
    exit();
}

// Garante que o player nao vai mudar de equipe (EU ACHO QUE ISSO NAO É NECESSARIO, MAS VAI QUE O CARA É MUITO HACKER) (e também não sei se funciona :/)
if($existsRoom['rooCode'] == $battleCheck['batRooCode']){
    if($ownerRoom['rooPlaCode1'] == $plaCode){
        if($teamId != $battleCheck['batTeaCode1'] && $battleCheck['batTeaCode1'] != 0){
            $_SESSION['message'] = 'Detectado alteração no time. | Safadenho >:( |';
            $_SESSION['battle']['teamId'] = $battleCheck['batTeaCode1'];
            header("Location: ../View/battle.php?roomCode=$roomCode&teamId=$battleCheck[batTeaCode1]");
            exit();
        }
    }
    if($ownerRoom['rooPlaCode2'] == $plaCode){
        if($teamId != $battleCheck['batTeaCode2'] && $battleCheck['batTeaCode2'] != 0){
            $_SESSION['message'] = 'Detectado alteração no time. | Safadenho >:( |';
            $_SESSION['battle']['teamId'] = $battleCheck['batTeaCode2'];
            header("Location: ../View/battle.php?roomCode=$roomCode&teamId=$battleCheck[batTeaCode2]");
            exit();
        }
    }
}


// Cria a Batalha na tabela Battle (APENAS PARA O USUÁRIO QUE CRIOU A SALA)
if ($plaCode == $ownerRoom['rooPlaCode1']) {
    if($battleCheck != null){
        $_SESSION['message'] = 'Batalha já criada.';
        $_SESSION['battle']['teamId'] = $teamId;
        header("Location: ../View/battle.php?roomCode=$roomCode&teamId=$teamId");
        exit();
    }
    try{
        $addTeamBattle = $conn->prepare("INSERT INTO battle (batRooCode, batTeaCode1) VALUES (?, ?)");
        $addTeamBattle->execute([$roomCode, $teamId]);
        $updateStatusReady = $conn->prepare("UPDATE room SET rooIsReadyPlayer1 = TRUE WHERE rooCode = ?");
        $updateStatusReady->execute([$roomCode]);
        $_SESSION['battle']['teamId'] = $teamId;
        header("Location: ../View/battle.php?roomCode=$roomCode&teamId=$teamId");
        exit();
    }
    catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao adicionar time na batalha: " . $e->getMessage();
        exit();
    }
}
else if($plaCode == $ownerRoom['rooPlaCode2']){
    if($battleCheck == null){
        $_SESSION['message'] = 'Espere o anfitrião iniciar a batalha.';
        header("Location: ../View/lobby.php");
        exit();
    }

    try{
        $addTeamBattle = $conn->prepare("UPDATE battle SET batTeaCode2 = ? WHERE batRooCode = ?");
        $addTeamBattle->execute([$teamId, $roomCode]);
        $updateStatusReady = $conn->prepare("UPDATE room SET rooIsReadyPlayer2 = TRUE WHERE rooCode = ?");
        $updateStatusReady->execute([$roomCode]);
        $_SESSION['battle']['teamId'] = $teamId;
        header("Location: ../View/battle.php?roomCode=$roomCode&teamId=$teamId");
        exit();
    }
    catch(PDOException $e) {
        $_SESSION['message'] = 'Bruh.' . $e->getMessage();
        exit();
    }
}
else{
    $_SESSION['message'] = 'Como você conseguiu chegar neste erro???';
    header("Location: ../View/roomList.php");
    exit();
}
?>