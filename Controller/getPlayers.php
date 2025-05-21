<?php
    require_once("connection.php");

    if(!isset($_SESSION['roomCode'])){
        header("Location: ../View/lobby.php");
        exit();
    }

    $roomCode = $_SESSION['roomCode'];

    //Pega os dois players logados na batalha
    try{
        $getPlayersStmt = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
        $getPlayersStmt->execute([$roomCode]);
        $getPlayers = $getPlayersStmt->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        $_SESSION['message'] = "Error: " . $e;
    }

    //Armazena os players em sessoes para serem usados depois
    
    $_SESSION['battle']['player1'] = $getPlayers['rooPlaCode1'];
    $_SESSION['battle']['player2'] = $getPlayers['rooPlaCode2'];
    return;
?>