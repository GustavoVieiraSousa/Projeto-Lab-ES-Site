<?php
//=================================================
// Esta página atualiza a lista de salas pelo PHP
//=================================================

require_once('connection.php');
session_start();

//Verifica se o roomList foi setado
if(!isset($_POST['roomList'])){
    $_SESSION['message'] = 'Você não está logado!';
    header("Location: ../View/roomList.php");
    exit();
}

//Atualiza a lista de salas
try{
    $roomListStmt = $conn->prepare("SELECT * FROM room");
    $roomListStmt->execute([]);
    $roomList = $roomListStmt->fetchAll(PDO::FETCH_ASSOC);
    $roomListCount = count($roomList);
    //se não houver salas, setar roomList como null
    if($roomListCount == 0){
        $_SESSION['roomList'] = null;
    }
}
catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}

$_SESSION['roomList'] = $roomList;
header("Location: ../View/roomList.php");
exit();
?>