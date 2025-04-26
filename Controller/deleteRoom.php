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

//Verifica se o player é dono da sala
try{
    $roomCheckStmt = $conn->prepare("SELECT * FROM room WHERE rooPlaCode1 = ? OR rooPlaCode2 = ?");
    $roomCheckStmt->execute([$plaCode, $plaCode]);
    $roomCheck = $roomCheckStmt->fetch(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}
if($roomCheck == null){
    $_SESSION['message'] = 'Não está em uma sala.';
    header("Location: ../View/roomList.php");
    exit();
}

//Deletando a sala
try{
    if($roomCheck['rooPlaCode1'] == $plaCode){
        $deleteRoom = $conn->prepare("DELETE FROM room WHERE rooPlaCode1 = ?");
        $deleteRoom->execute([$plaCode]);
        $_SESSION['message'] = 'Sala deletada com sucesso!';
    }
    else{
        $stmt = $conn->prepare("UPDATE room SET rooPlaCode2 = NULL WHERE rooPlaCode2 = ?");
        $stmt->execute([$plaCode]);
        $_SESSION['message'] = 'Saiu da sala com sucesso';
    }
}
catch(PDOException $e){
    $conn->rollBack();
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}

unset($_SESSION['roomCode']);
header("Location: ../View/roomList.php");
exit();
?>