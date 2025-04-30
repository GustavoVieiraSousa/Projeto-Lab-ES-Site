<?php
require_once('connection.php');
session_start();

if(!isset($_SESSION['plaCode'])){
    exit();
}
if(!isset($_SESSION['roomCode'])){
    exit();
}
if(!isset($_POST['editRoom'])){
    exit();
}

$plaCode = $_SESSION['plaCode'];
$roomCode = $_SESSION['roomCode'];

$roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
$roomCheckStmt->execute([$plaCode, $plaCode]);
$roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);


if(!$roomCheck == null){
    header("Location: /PageOfShameEditRoom.php");
    exit();
}

//adicionando player 2 na sala
try{
    $stmt = $conn->prepare("ALTER room SET rooPlaCode2 = :plaCode WHERE rooCode = :roomCode");
    $stmt->bindParam(':plaCode', $plaCode, PDO::PARAM_INT);
    $stmt->bindParam(':roomCode', $roomCode, PDO::PARAM_INT);
    $stmt = $conn->execute();
}
catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar time: ' . $e->getMessage());
}

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>''