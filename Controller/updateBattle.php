<?php
    require_once("connection.php");
    session_start();

    $plaCode = $_SESSION['plaCode'];
    $player1 = $_SESSION['battle']['player1'];
    $player2 = $_SESSION['battle']['player2'];
    $roomCode = $_SESSION['roomCode'];
    $teamId = $_SESSION['battle']['teamId'];

    //Pega todas as informacoes da tabela Battle
    try{
        $getBattleStmt = $conn->prepare("SELECT * FROM battle WHERE batRooCode = ?");
        $getBattleStmt->execute([$roomCode]);
        $getBattle = $getBattleStmt->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        $_SESSION['message'] = "Erro ao pegar as informações da Battle: " . $e;
    }

    $teamBattle1 = $getBattle['batTeaCode1'];
    $teamBattle2 = $getBattle['batTeaCode2'];
    $round = $getBattle['batRound'];
    $damageDealtByPlayer1 = $getBattle['batDmgCounterPlayer1'];
    $damageDealtByPlayer2 = $getBattle['batDmgCounterPlayer2'];

    //Pegar as informações do Pokemon OnField do Player 1 e Player 2
    try{
        //Stats do Pokemon do Player 1
        $getStatsPlayer1Stmt = $conn->prepare(
            "SELECT * FROM pokemon WHERE pokIsOnField = true AND pokCode IN 
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
                SELECT teaPokCode6 FROM team WHERE teaPlaCode = :plaCode
            )");

        //Stats do Pokemon do Player 2
        $getStatsPlayer2Stmt = $conn->prepare(
            "SELECT * FROM pokemon WHERE pokIsOnField = true AND pokCode IN 
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
                SELECT teaPokCode6 FROM team WHERE teaPlaCode = :plaCode
            )");

        $getStatsPlayer1Stmt->execute([':plaCode' => $player1]);
        $getStatsPlayer1 = $getStatsPlayer1Stmt->fetch(PDO::FETCH_ASSOC);

        $getStatsPlayer2Stmt->execute([':plaCode' => $player2]);
        $getStatsPlayer2 = $getStatsPlayer2Stmt->fetch(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        $_SESSION['message'] = "Erro ao Buscar : " . $e;
    }

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonIdPlayer1DB = $getStatsPlayer1['pokId'];
    $basicAttackPlayer1DB = $getStatsPlayer1['pokBasicAttack'];
    $speedPlayer1DB = $getStatsPlayer1['pokSpeed'];
    $hpPlayer1DB = $getStatsPlayer1['pokHp'];

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonIdPlayer2DB = $getStatsPlayer2['pokId'];
    $basicAttackPlayer2DB = $getStatsPlayer2['pokBasicAttack'];
    $speedPlayer2DB = $getStatsPlayer2['pokSpeed'];
    $hpPlayer2DB = $getStatsPlayer2['pokHp'];

    //Separando o HP para nao precisar atualizar o hp no Banco de Dados
    $hpPlayer1 = $hpPlayer1DB;
    $hpPlayer2 = $hpPlayer2DB;

    //Verificar qual pokemon age primeiro
    if($speedPlayer1DB >= $speedPlayer2DB){
        //Player 1 age primeiro que o Player 2 (pode ser dano, defesa, etc...)
        damage($pokemonIdPlayer1DB, $basicAttackPlayer1DB, $player1DB);
        isDead($hpPlayer2) ? changePokemon() : "";
    }
    else{
        damage($pokemonIdPlayer2DB, $basicAttackPlayer2DB, $player2DB);
        isDead($hpPlayer1) ? changePokemon() : "";
    }

    function damage($pokemon, $damage, $player){

    }

    function isDead($hpPlayer){ return $hpPlayer <= 0; }

    function changePokemon(){
        if(isTheLastPokemon()){
            $_SESSION['battle']['win'] = 0;
        }
        else{
            
        }
    }

    function isTheLastPokemon(){

    }
?>