<?php

//====================================================
// Esta página adiciona uma uma sala para o player 1
//====================================================

require_once('connection.php');
session_start();

//Verifica se o player está logado
if(!isset($_SESSION['plaCode'])){
    $_SESSION['message'] = 'Você não está logado!';
    header("Location: ../View/roomList.php");
    exit();
}

$plaCode = $_SESSION['plaCode'];

//Verifica se o player já está em uma sala
try{
    $roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
    $roomCheckStmt->execute([$plaCode, $plaCode]);
    $roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);
    if(!$roomCheck == null){
        $_SESSION['message'] = 'Você já está em uma sala!';
        header("Location: ../View/roomList.php");
        exit();
    }
}catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}

//criando a sala
$stmt = $conn->prepare("INSERT INTO room (rooPlaCode1) VALUES (?)");
$stmt->execute([$plaCode]);
$roomCode = $conn->lastInsertId();

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>