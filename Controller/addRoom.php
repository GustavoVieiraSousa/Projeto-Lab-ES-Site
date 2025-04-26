<?php
require_once('connection.php');
session_start();

if(!isset($_SESSION['plaCode'])){
    exit();
}
if(!isset($_POST["addRoom"])){
    exit();
}

$plaCode = $_SESSION['plaCode'];

$roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
$roomCheckStmt->execute([$plaCode, $plaCode]);
$roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);

if(!$roomCheck == null){
    header("Location: /PageOfShameAddRoom.php");
    exit();
}

//criando a Room
$stmt = $conn->prepare("INSERT INTO room (rooPlaCode1) VALUES (?)");
$stmt->execute([$plaCode]);
$roomCode = $conn->lastInsertId();

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>