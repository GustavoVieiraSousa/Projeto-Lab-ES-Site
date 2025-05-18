<?php
//=================================================
// Esta página atualiza a lista de salas pelo AJAX
//=================================================

require_once('connection.php');
session_start();

//Verifica se o roomList foi setado
if (!isset($_SESSION['roomList'])) {
    echo json_encode([]);
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
    error_log('Erro ao criar Sala: ' . $e->getMessage());
}

$_SESSION['roomList'] = $roomList;
echo json_encode($_SESSION['roomList']);
exit();
?>