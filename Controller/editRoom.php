<?php

//=================================================
// Esta página edita a sala do player pelo PHP
//=================================================

require_once('connection.php');
session_start();

//Verifica se o player está logado
if(!isset($_SESSION['plaCode'])){
    $_SESSION['message'] = 'Você não está logado!';
    header("Location: ../View/roomList.php");
    exit();
}

//Verifica se o roomCode foi setado
if(!isset($_POST['roomCode'])){
    $_SESSION['message'] = 'Erro (roomCode não setado)';
    header("Location: ../View/roomList.php");
    exit();
}

//Verifica se o editRoom foi setado
if(!isset($_POST['editRoom'])){
    $_SESSION['message'] = 'Erro (editRoom não setado)';
    header("Location: ../View/roomList.php");
    exit();
}

$plaCode = $_SESSION['plaCode'];
$roomCode = $_POST['roomCode'];

//Verifica se a sala existe
try{
    $roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
    $roomCheckStmt->execute([$roomCode]);
    $roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);
    if($roomCheck == null){
        $_SESSION['message'] = 'Sala não encontrada!';
        header("Location: ../View/roomList.php");
        exit();
    }
}
catch(PDOException $e){
    error_log('Erro ao entrar na sala: ' . $e->getMessage());
}

//Verifica se o player já está em uma sala
try{
    $playerCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
    $playerCheckStmt->execute([$plaCode, $plaCode]);
    $playerCheck = $playerCheckStmt->fetch(PDO::FETCH_ASSOC);
    if(!$playerCheck == null){
        $_SESSION['message'] = 'Você já está em uma sala!';
        header("Location: ../View/lobby.php");
        exit();
    }
}catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}

//verifica se a sala ja está cheia
try{
    $roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooCode = ? AND rooPlaCode2 IS NOT NULL");
    $roomCheckStmt->execute([$roomCode]);
    $roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);
    if($roomCheck != null){
        $_SESSION['message'] = 'Sala cheia!';
        header("Location: ../View/roomList.php");
        exit();
    }
}
catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao entrar na sala: ' . $e->getMessage());
}

//adicionando player 2 na sala
try{
    $stmt = $conn->prepare("UPDATE room SET rooPlaCode2 = ? WHERE rooCode = ?");
    $stmt->execute([$plaCode, $roomCode]);
}
catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar time: ' . $e->getMessage());
}

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>