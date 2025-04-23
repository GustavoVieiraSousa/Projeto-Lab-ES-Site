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

//criando a Room
$stmt = $conn->prepare("INSERT INTO room (rooPlaCode1) VALUES (?)");
$stmt->execute([$plaCode]);
$roomCode = $conn->lastInsertId();

$_SESSION['roomCode'] = $roomCode;
header("Location: ../View/roomList.php");
exit();
?>