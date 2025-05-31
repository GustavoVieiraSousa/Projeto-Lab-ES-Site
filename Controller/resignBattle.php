<?php
    require_once("connection.php");
    session_start();

    //verifica se o botao "Desistir" foi pressionado na battle.php
    if(!isset($_POST['resign'])){
        $_SESSION['message'] = "Método inválido.";
        exit();
    }
    //verifica se o "teamId" foi setado
    if(!isset($_SESSION['battle']['teamId'])){
        $_SESSION['message'] = "TeamId não Setado.";
        exit();
    }
    // Verifica se o jogador está logado
    if(!isset($_SESSION['plaCode'])) { 
        $_SESSION['message'] = "Jogador não registrado.";
        exit();
    }
    // Verifica se a sala está instanciada na sessao
    if(!isset($_SESSION['roomCode'])) {
        $_SESSION['message'] = "Sala não registrada.";
        exit();
    }

    $teamId = $_SESSION['battle']['teamId'];
    $plaCode = $_SESSION['plaCode'];
    $roomCode = $_SESSION['roomCode'];

    //Zera o ataque para o cliente que NAO desistiu, nao fique preso em um loop infinito
    function clearAttackPlayer1(){
        global $conn, $roomCode;
        $clearDmgStmt = $conn->prepare("UPDATE battle SET batDmgCounterPlayer1 = NULL WHERE batRooCode = ?");
        $clearDmgStmt->execute([$roomCode]);
    }

    function clearAttackPlayer2(){
        global $conn, $roomCode;
        $clearDmgStmt = $conn->prepare("UPDATE battle SET batDmgCounterPlayer2 = NULL WHERE batRooCode = ?");
        $clearDmgStmt->execute([$roomCode]);
    }

    //Tira os dois players do estado "Pronto"
    function setIsReadyNull(){
        global $conn, $roomCode;
        try{
            $isReadyNullStmt = $conn->prepare("UPDATE room SET rooIsReadyPlayer1 = NULL, rooIsReadyPlayer2 = NULL WHERE rooCode = ?");
            $isReadyNullStmt->execute([$roomCode]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro Set Is Ready Null: ".$e;
            error_log($e);
            exit();
        }
    }

    // Pesquisa quem é o dono da sala (tratamento)
    try{
        $ownerRoom = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
        $ownerRoom->execute([$roomCode]);
        $ownerRoom = $ownerRoom->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e) {
        $_SESSION['message'] = "Erro ao buscar dono da sala: " . $e->getMessage();
        exit();
    }

    //Retirando o Time da tabela Battle para marcar vitória
    try{
        if($ownerRoom['rooPlaCode1'] == $plaCode){
            $resignStmt = $conn->prepare("UPDATE battle SET batTeaCode1 = NULL WHERE batRooCode = ?");
            $resignStmt->execute([$roomCode]);
            $_SESSION['battle']['win'] = 0;
        }
        if($ownerRoom['rooPlaCode2'] == $plaCode){
            $resignStmt = $conn->prepare("UPDATE battle SET batTeaCode2 = NULL WHERE batRooCode = ?");
            $resignStmt->execute([$roomCode]);
            $_SESSION['battle']['win'] = 0;
        }
    }
    catch(PDOException $e){
        $_SESSION['message'] = 'Erro ao "Desistir": ' . $e->getMessage();
    }

    //Unset all pokemons OnField state (set the state to NULL)
    try{
        $unsetOnFieldStmt = $conn->prepare(
            "UPDATE pokemon SET pokIsOnField = NULL WHERE pokCode IN (
                SELECT teaPokCode1 FROM team WHERE teaPlaCode = :plaCode
                UNION ALL
                SELECT teaPokCode2 FROM team WHERE teaPlaCode = :plaCode
                UNION ALL
                SELECT teaPokCode3 FROM team WHERE teaPlaCode = :plaCode
                UNION ALL
                SELECT teaPokCode4 FROM team WHERE teaPlaCode = :plaCode
                UNION ALL
                SELECT teaPokCode5 FROM team WHERE teaPlaCode = :plaCode
                UNION ALL
                SELECT teaPokCode6 FROM team WHERE teaPlaCode = :plaCode
            );"
        );
        $unsetOnFieldStmt->execute([':plaCode' => $plaCode]);
    }
    catch(PDOException $e){
        $_SESSION['message'] = 'Erro Unset IsOnField: ' . $e->getMessage();
    }

    //Zera o ataque para o cliente que NAO desistiu, nao fique preso em um loop infinito
    clearAttackPlayer1(); 
    clearAttackPlayer2();
    
    //Tira os dois players do estado "Pronto"
    setIsReadyNull();

    unset($_SESSION['battle']);
    header("Location:../View/lobby.php");
    exit();
?>