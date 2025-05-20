<?php
    require_once("connection.php");
    session_start();

    if(isset($_SESSION['end'])){
        $_SESSION['message'] = "Batalha finalizada.";
        header("lobby.php");
        exit();
    }

    $plaCode = $_SESSION['plaCode'];
    $player1 = $_SESSION['battle']['player1'];
    $player2 = $_SESSION['battle']['player2'];
    $roomCode = $_SESSION['roomCode'];
    $teamId = $_SESSION['battle']['teamId'];

    //Pega todas as informacoes da tabela Battle
    function getBattle(){
        require_once("connection.php");
        try{
            $getBattleStmt = $conn->prepare("SELECT * FROM battle WHERE batRooCode = ?");
            $getBattleStmt->execute([$roomCode]);
            $getBattle = $getBattleStmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao pegar as informações da Battle: " . $e;
            exit();
        }

        return $getBattle;
    }

    //Verifica se a batalha acabou (provavelmente vai fazer com que o player vencedor jogue por mais um turno)
    $getBattle = getBattle();
    if($getBattle['batTeaCode1'] == 0 || $getBattle['batTeaCode2'] == 0){
        endBattle();
    }

    function getPokemonPlayer1(){
        require_once("connection.php");
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

            $getStatsPlayer1Stmt->execute([':plaCode' => $player1]);
            $getStatsPlayer1 = $getStatsPlayer1Stmt->fetch(PDO::FETCH_ASSOC);

            return $getStatsPlayer1;
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Buscar Player1: " . $e;
            exit();
        }
    }
    
    function getPokemonPlayer2(){
        require_once("connection.php");
        try{
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

            $getStatsPlayer2Stmt->execute([':plaCode' => $player2]);
            $getStatsPlayer2 = $getStatsPlayer2Stmt->fetch(PDO::FETCH_ASSOC);

            return $getStatsPlayer2;
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Buscar Player2: " . $e;
            exit();
        }
    }
    
    $getStatsPlayer1 = getPokemonPlayer1();
    $getStatsPlayer2 = getPokemonPlayer2();

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonIdPlayer1DB = $getStatsPlayer1['pokId'];
    $basicAttackPlayer1DB = $getStatsPlayer1['pokBasicAttack'];
    $basicDefensePlayer1DB = $getStatsPlayer1['pokBasicDefense'];
    $speedPlayer1DB = $getStatsPlayer1['pokSpeed'];
    $hpPlayer1DB = $getStatsPlayer1['pokHp'];

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonIdPlayer2DB = $getStatsPlayer2['pokId'];
    $basicAttackPlayer2DB = $getStatsPlayer2['pokBasicAttack'];
    $basicDefensePlayer2DB = $getStatsPlayer2['pokBasicDefense'];
    $speedPlayer2DB = $getStatsPlayer2['pokSpeed'];
    $hpPlayer2DB = $getStatsPlayer2['pokHp'];

    //Separando o HP para nao precisar atualizar o hp no Banco de Dados
    if(!isset($_SESSION['battle']['hpPlayer1']) && $_SESSION['battle']['hpPlayer2']){
        $_SESSION['battle']['hpPlayer1'] = $hpPlayer1 = $hpPlayer1DB;
        $_SESSION['battle']['hpPlayer2'] = $hpPlayer2 = $hpPlayer2DB;
    }
    else{
        $hpPlayer1 = $hpPlayer1DB;
        $hpPlayer2 = $hpPlayer2DB;
    }

    //Verificar qual pokemon age primeiro
    if($speedPlayer1DB >= $speedPlayer2DB){
        $hpPlayer2 = attackPlayer1($hpPlayer2);
        updateHp($hpPlayer2, $pokemonIdPlayer2DB);

        if(isDead($hpPlayer2)){
            setIsDeadPokemonPlayer2($pokemonIdPlayer2DB);
            changePokemon($pokemonIdPlayer2DB, $player2);
        }
        else{
            $hpPlayer1 = attackPlayer2($hpPlayer1);
            updateHp($hpPlayer1, $pokemonIdPlayer1DB);

            if(isDead($hpPlayer1)){
                setIsDeadPokemonPlayer1($pokemonIdPlayer1DB);
                changePokemon($pokemonIdPlayer1DB, $player1);
            }
        }
        
        return;
    }

    else{
        $hpPlayer1 = attackPlayer2($hpPlayer1);
        updateHp($hpPlayer1, $pokemonIdPlayer1DB);

        if(isDead($hpPlayer1)){
            setIsDeadPokemonPlayer1($pokemonIdPlayer1DB);
            changePokemon($pokemonIdPlayer1DB, $player1);
            return;
        }
        else{
            $hpPlayer2 = attackPlayer1($hpPlayer2);
            updateHp($hpPlayer2, $pokemonIdPlayer2DB);

            if(isDead($hpPlayer2)){
                setIsDeadPokemonPlayer2($pokemonIdPlayer2DB);
                changePokemon($pokemonIdPlayer2DB, $player2);
            }
        }

        return;
    }

    //Pega o dano do BD e faz o calculo para tirar a vida do pokemon Player1
    function attackPlayer1($hp){
        require_once("connection.php");
        $getBattle = getBattle();

        $damageDealtByPlayer1 = $getBattle['batDmgCounterPlayer1'];

        $hp = $damageDealtByPlayer1 - $hp;

        return $hp;
    }

    //Pega o dano do BD e faz o calculo para tirar a vida do pokemon Player2
    function attackPlayer2($hp){
        require_once("connection.php");
        $getBattle = getBattle();
        $damageDealtByPlayer2 = $getBattle['batDmgCounterPlayer2'];
            
        $hp = $damageDealtByPlayer2 - $hp;

        return $hp;
    }


    function isDead($hpPlayer){ return $hpPlayer <= 0; }

    //Troca o pokemon que morreu em batalha
    function changePokemon($pokemon, $player){
        $resultLastPokemon = isTheLastPokemon($player); //Verifica se é o ultimo pokemon vivo

        //TRUE -> Todos os pokemons morreram | $resultLastPokemon -> Ainda tem pokemon vivo | FALSE -> Deu um erro muito grande
        if($resultLastPokemon == true){
            defeat($player);
            endBattle();
        }
        else if($resultLastPokemon == $pokemon+1){
            require_once("connection.php");
            $unsetIsOnFieldStmt = $conn->prepare("UPDATE pokemon SET pokIsOnField = NULL WHERE pokCode = ?");
            $setIsOnFieldStmt = $conn->prepare("UPDATE pokemon SET pokIsOnField = 1 WHERE pokCode = ?");

            $unsetIsOnFieldStmt->execute([$pokemon]);
            $setIsOnFieldStmt->execute([$resultLastPokemon]);
        }
        else{
            $_SESSION['message'] = "GRANDE ERRO | isTheLastPokemon() retornou FALSE:";
            exit();
        }
    }

    //Seta IsDead na tabela Pokemon pro Player1
    function setIsDeadPokemonPlayer1($pokemon){
        require_once("connection.php");
        $hpPlayer1 = $_SESSION['battle']['hpPlayer1'];

        try{
            $setIsDeadStmt = $conn->prepare("UPDATE pokemon SET pokIsDead = 1, pokHp = ? WHERE pokCode = ?");
            $setIsDeadStmt->execute([$hpPlayer1, $pokemon]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Setar IsDeadPokemonPlayer1: ".$e;
            exit();
        }
        
    }

    //Seta IsDead na tabela Pokemon pro Player2
    function setIsDeadPokemonPlayer2($pokemon){
        require_once("connection.php");
        $hpPlayer2 = $_SESSION['battle']['hpPlayer2'];

        try{
            $setIsDeadStmt = $conn->prepare("UPDATE pokemon SET pokIsDead = 1, pokHp = ? WHERE pokCode = ?");
            $setIsDeadStmt->execute([$hpPlayer2, $pokemon]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Setar IsDeadPokemonPLayer2: ".$e;
            exit();
        }
        
    }

    //Verifica se todos os pokemons da equipe estão mortos
    function isTheLastPokemon($player){
        try{
            require_once("connection.php");
            $lastPokemonStmt = $conn->prepare("SELECT pokId FROM pokemon WHERE pokIsDead = 1 AND pokCode IN 
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
            $lastPokemonStmt->execute([':plaCode' => $player]);
            $lastPokemon = $lastPokemonStmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao buscar pokemons IsDead: ".$e;
            exit();
        }
        
        //Caso nao retorne nada (teoricamente impossivel pois pokIsDead é setado para o primeiro pokemon antes de passar aqui)
        if($lastPokemon == NULL){
            return false;
        }

        $i = 0;
        while ($lastPokemon){
            $pokemonDead[$i] = $lastPokemon;
            $i++;
        }
        if(sizeof($pokemonDead) == 6){
            return true; //Todos os pokemons da equipe estao mortos
        }

        $pokemonNotDead = $pokemonDead[$i-1];
        return $pokemonNotDead+1; //Volta o proximo pokemon do time
    }

    //Atualiza o HP do Pokemon OnField no BD
    function updateHp($hp, $pokemon){
        require_once("connection.php");
        try{
            $updateHpStmt = $conn->prepare("UPDATE pokemon SET pokHp = ? WHERE pokCode = ?");
            $updateHpStmt->execute([$hp, $pokemon]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Atualizar HP." . $e;
            exit();
        }
        
    }

    //Derrota para o player que nao possui mais pokemons na batalha
    function defeat($player){
        require_once("connection.php");
        $roomCode = $_SESSION['roomCode'];

        //Verifica se o player é o batTeaCode1 ou batTeaCode2
        try{
            $findTeamStmt = $conn->prepare(
                "SELECT 
                    CASE
                        WHEN t1.teaPlaCode = :plaCode THEN 'player_is_batTeaCode1'
                        WHEN t2.teaPlaCode = :plaCode THEN 'player_is_batTeaCode2'
                        ELSE 'player_not_in_battle'
                    END AS player_position
                FROM battle b
                LEFT JOIN team t1 ON b.batTeaCode1 = t1.teaCode
                LEFT JOIN team t2 ON b.batTeaCode2 = t2.teaCode
                WHERE b.batRooCode = :roomCode
                LIMIT 1;");

            $findTeamStmt->execute([':plaCode' => $player, ':roomCode' => $roomCode]);
            $teamDefeated = $findTeamStmt->fetch(PDO::FETCH_ASSOC);

            if ($teamDefeated) {
                if ($teamDefeated['player_position'] === 'player_is_batTeaCode1') {
                    try{
                        $defeatStmt = $conn->prepare("UPDATE battle SET batTeaCode1 = 0 WHERE roomCode = ?");
                        $defeatStmt->execute([$roomCode]);
                        $_SESSION['battle']['defeat'] = $player;
                    }
                    catch(PDOException $e){
                        $_SESSION['message'] = "Erro ao setar Derrotado: ".$e;
                        exit();
                    }

                } elseif ($teamDefeated['player_position'] === 'player_is_batTeaCode2') {
                    try{
                        $defeatStmt = $conn->prepare("UPDATE battle SET batTeaCode1 = 0 WHERE roomCode = ?");
                        $defeatStmt->execute([$roomCode]);
                        $_SESSION['battle']['defeat'] = $player;
                    }
                    catch(PDOException $e){
                        $_SESSION['message'] = "Erro ao setar Derrotado: ".$e;
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Erro ao setar teamDefeat: ".$e;
                    exit();
                }
            }       
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao buscar Player: ".$e;
            exit();
        }
    }

    //Finaliza a batalha
    function endBattle(){
        
        $plaCode = $_SESSION['plaCode'];
        $player = $_SESSION['battle']['defeat'];

        //Se o jogador for o perdedor, marca na sessao
        if($plaCode == $player){
            $_SESSION['win'] = 0;
        }
        else{
            $_SESSION['win'] = 1;
        }
        $_SESSION['end'] = 1;

        //===========================================================
        //==================  CHAMAR O AJAX AQUI ====================
        //===========================================================

        unset($_SESSION['battle']);
        return;
    }
?>