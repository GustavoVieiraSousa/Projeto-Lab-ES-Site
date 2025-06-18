<?php
require_once('connection.php');
session_start();

if(!isset($_SESSION['plaCode'])){
    header("Location: ../View/login.php");
    exit();
}
if(!isset($_POST['roomCode'])){
    header("Location: ../View/roomList.php");
    exit();
}
if(!isset($_POST['editRoom'])){
    header("Location: ../View/roomList.php");
    exit();
}

$plaCode = $_SESSION['plaCode'];
$roomCode = $_POST['roomCode'];

//adicionando player 2 na sala
try{
    $stmt = $conn->prepare("UPDATE room SET rooPlaCode2 = :plaCode WHERE rooCode = :roomCode");
    $stmt->bindParam(':plaCode', $plaCode, PDO::PARAM_INT);
    $stmt->bindParam(':roomCode', $roomCode, PDO::PARAM_INT);
    $stmt->execute();
}
catch(PDOException $e){
    error_log('Erro ao criar time: ' . $e->getMessage());
}

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>