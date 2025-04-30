<?php

//====================================================================
// Esta página deleta a sala do player 1 e remove da sala o player 2
//====================================================================

require_once('connection.php');
session_start();

//Verifica se o player está logado
if(!isset($_SESSION['plaCode'])){
    $_SESSION['message'] = 'Você não está logado!';
    header("Location: ../View/roomList.php");
    exit();
}

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

//Verifica se o player é dono da sala
try{
    $roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
    $roomCheckStmt->execute([$plaCode, $plaCode]);
    $roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    error_log('Erro ao identificar player: ' . $e->getMessage());
    $_SESSION['message'] = 'Falha ao identificar player: ' . $e->getMessage();
    exit();
}
if($roomCheck == null){
    $_SESSION['message'] = 'Não está em uma sala.';
    header("Location: ../View/roomList.php");
    exit();
}

//verifica se tem alguma battle criada
try{
    $battleCheckStmt = $conn->prepare("SELECT * FROM battle WHERE batRooCode = ?");
    $battleCheckStmt->execute([$roomCode]);
    $battleCheck = $battleCheckStmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e){
    error_log('Erro ao criar Sala: ' . $e->getMessage());
    $_SESSION['message'] = 'Falha ao buscar batalha: ' . $e->getMessage();
    exit();
}

//Deletando a sala
try{
    //é o player criador da sala
    if($roomCheck['rooPlaCode1'] == $plaCode){
        //caso battle criada
        if($battleCheck != null){
            $deleteBattle = $conn->prepare("DELETE FROM battle WHERE batRooCode = ?");
            $deleteBattle->execute([$roomCheck['rooCode']]);
        }
        $deleteRoom = $conn->prepare("DELETE FROM room WHERE rooPlaCode1 = ?");
        $deleteRoom->execute([$plaCode]);
    }
    else{
        $stmt = $conn->prepare("UPDATE room SET rooPlaCode2 = NULL WHERE rooPlaCode2 = ?");
        $stmt->execute([$plaCode]);
    }
}
catch(PDOException $e){
    error_log('Erro ao criar Sala: ' . $e->getMessage());
    $_SESSION['message'] = $battleCheck['rooPlaCode1'] . 'Falha ao tentar apagar' . $e->getMessage();
    exit();
}

//barrar id de sala

//apagar battle quando player sair




unset($_SESSION['roomCode']);
header("Location: ../View/roomList.php");
exit();
?>