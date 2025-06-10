<?php
require_once("connection.php");

$player1 = $_SESSION['battle']['player1'];
$player2 = $_SESSION['battle']['player2'];

    try{
        $resetPokemonStmt = $conn->prepare("UPDATE pokemon SET pokHp = pokMaxHp WHERE pokCode IN 
            (SELECT teaPokCode1 FROM team WHERE teaPlaCode = :plaCode
            UNION ALL
            SELECT teaPokCode2 FROM team WHERE teaPlaCode = :plaCode
            UNION ALL
            SELECT teaPokCode3 FROM team WHERE teaPlaCode = :plaCode
            UNION ALL
            SELECT teaPokCode4 FROM team WHERE teaPlaCode = :plaCode
            UNION ALL
            SELECT teaPokCode5 FROM team WHERE teaPlaCode = :plaCode
            UNION ALL
            SELECT teaPokCode6 FROM team WHERE teaPlaCode = :plaCode)");
        $resetPokemonStmt->execute([':plaCode' => $player1]);
        $resetPokemonStmt->execute([':plaCode' => $player2]);
    }
    catch(PDOException $e){
        $_SESSION['message'] = "Erro ao Resetar Pokemon: ".$e;
        exit();
    }

    exit();
?>