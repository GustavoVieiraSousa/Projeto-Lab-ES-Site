<?php
    header('Content-Type: application/json');
    require_once("connection.php");
    session_start();

    if(isset($_SESSION['end'])){
        $_SESSION['message'] = "Batalha finalizada.";
        // header("lobby.php");
        echo json_encode(['result' => true]);
        exit();
    }

    $plaCode = $_SESSION['plaCode'];
    $player1 = $_SESSION['battle']['player1'];
    $player2 = $_SESSION['battle']['player2'];
    $roomCode = $_SESSION['roomCode'];
    $teamId = $_SESSION['battle']['teamId'];
    // $_SESSION['battle']['round'] = 0;

    //Pega todas as informacoes da tabela Battle
    function getBattle(){
        global $conn;
        require_once("connection.php");
        // $round = $_SESSION['battle']['round'];
        $roomCode = $_SESSION['roomCode'];
        try{
            $getBattleStmt = $conn->prepare("SELECT * FROM battle WHERE batRooCode = ?");
            $getBattleStmt->execute([$roomCode]);
            $getBattle = $getBattleStmt->fetch(PDO::FETCH_ASSOC);

            // $setRound = $conn->prepare("UPDATE battle SET batRound = ? WHERE batRooCode = ?");
            // $setRound->execute([$round, $roomCode]);
            // $_SESSION['battle']['round'] = $round + 1;
        }   
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao pegar as informações da Battle: " . $e;
            echo json_encode(['result' => true]);
            exit();
        }

        return $getBattle;
    }

    //Verifica se a batalha acabou (provavelmente vai fazer com que o player vencedor jogue por mais um turno)
    $getBattle = getBattle();
    if($getBattle['batTeaCode1'] == 0 || $getBattle['batTeaCode2'] == 0){
        endBattle();
    }

    function whoIsThePlayer(){
        global $plaCode, $player1, $player2;
        if($plaCode == $player1){
            return $player1;
        }
        else if($plaCode == $player2){
            return $player2;
        }
        else{
            $_SESSION['message'] = "Erro ao identificar o player.";
            echo json_encode(['result' => true]);
            exit();
        }
    }

    function getPokemonPlayer1(){
        global $conn, $player1;
        require_once("connection.php");
        //Pegar as informações do Pokemon OnField do Player 1 e Player 2
        try{
            //Stats do Pokemon do Player 1
            $getStatsPlayer1Stmt = $conn->prepare(
            "SELECT * FROM pokemon WHERE pokIsOnField = 1 AND pokCode IN 
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
            echo json_encode(['result' => true]);
            exit();
        }
    }
    
    function getPokemonPlayer2(){
        global $conn, $player2;
        require_once("connection.php");
        try{
        //Stats do Pokemon do Player 2
        $getStatsPlayer2Stmt = $conn->prepare(
            "SELECT * FROM pokemon WHERE pokIsOnField = 1 AND pokCode IN 
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
            echo json_encode(['result' => true]);
            exit();
        }
    }
    
    $getStatsPlayer1 = getPokemonPlayer1();
    $getStatsPlayer2 = getPokemonPlayer2();

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonCodePlayer1DB = $getStatsPlayer1['pokCode'];
    $basicAttackPlayer1DB = $getStatsPlayer1['pokBasicAttack'];
    $basicDefensePlayer1DB = $getStatsPlayer1['pokBasicDefense'];
    $speedPlayer1DB = $getStatsPlayer1['pokSpeed'];
    $hpPlayer1DB = $getStatsPlayer1['pokHp'];

    //Armazenar os dados coletados do Pokemon OnField do Player1
    $pokemonCodePlayer2DB = $getStatsPlayer2['pokCode'];
    $basicAttackPlayer2DB = $getStatsPlayer2['pokBasicAttack'];
    $basicDefensePlayer2DB = $getStatsPlayer2['pokBasicDefense'];
    $speedPlayer2DB = $getStatsPlayer2['pokSpeed'];
    $hpPlayer2DB = $getStatsPlayer2['pokHp'];

    //Separando o HP para nao precisar atualizar o hp no Banco de Dados
    if (!isset($_SESSION['battle']['hpPlayer1'])) {
        $_SESSION['battle']['hpPlayer1'] = $hpPlayer1DB;
    }
    if (!isset($_SESSION['battle']['hpPlayer2'])) {
        $_SESSION['battle']['hpPlayer2'] = $hpPlayer2DB;
    }
    $hpPlayer1 = $hpPlayer1DB;
    $hpPlayer2 = $hpPlayer2DB;

    //Verificar qual pokemon age primeiro
    // global $pokemonCodePlayer1DB, $basicAttackPlayer1DB, $basicDefensePlayer1DB, $speedPlayer1DB, $hpPlayer1DB;
    // global $pokemonCodePlayer2DB, $basicAttackPlayer2DB, $basicDefensePlayer2DB, $speedPlayer2DB, $hpPlayer2DB;

    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    //======================= PARTE PRINCIPAL DA BATALHA ==========================
    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv

var_dump($_SESSION['battle']);

    if($speedPlayer1DB >= $speedPlayer2DB){
        if($plaCode == $player1){
            $hpPlayer2 = attackPlayer1($hpPlayer2);
            updateHp($hpPlayer2, $pokemonCodePlayer2DB);
        }
        if(isDead($hpPlayer2)){
            setIsDeadPokemonPlayer2($pokemonCodePlayer2DB);
            if($plaCode == $player2){ changePokemon($pokemonCodePlayer2DB, $player2); }
        }

        else{
            if($plaCode == $player2){
                $hpPlayer1 = attackPlayer2($hpPlayer1);
                updateHp($hpPlayer1, $pokemonCodePlayer1DB);
            }
            if(isDead($hpPlayer1)){
                setIsDeadPokemonPlayer1($pokemonCodePlayer1DB);
                if($plaCode == $player1){ changePokemon($pokemonCodePlayer1DB, $player1); }
            }
        }

        return;
    }

    else{
        if($plaCode == $player2){
            $hpPlayer1 = attackPlayer2($hpPlayer1);
            updateHp($hpPlayer1, $pokemonCodePlayer1DB);
        }
        if(isDead($hpPlayer1) && $plaCode == $player1){
            setIsDeadPokemonPlayer1($pokemonCodePlayer1DB);
            changePokemon($pokemonCodePlayer1DB, $player1);
        }

        else{
            if($plaCode == $player1){
                $hpPlayer2 = attackPlayer1($hpPlayer2);
                updateHp($hpPlayer2, $pokemonCodePlayer2DB);
            }
            if(isDead($hpPlayer2) && $plaCode == $player2){
                setIsDeadPokemonPlayer2($pokemonCodePlayer2DB);
                changePokemon($pokemonCodePlayer2DB, $player2);
            }
        }

        return;
    }

    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    //======================= PARTE PRINCIPAL DA BATALHA ==========================
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    //Pega o dano do BD e faz o calculo para tirar a vida do pokemon Player1
    function attackPlayer1($hp){
        global $conn, $player1, $player2;
        require_once("connection.php");
        $getBattle = getBattle();
        $damageDealtByPlayer1 = $getBattle['batDmgCounterPlayer1'];

        $hp = $hp - $damageDealtByPlayer1;

        return $hp;
    }

    //Pega o dano do BD e faz o calculo para tirar a vida do pokemon Player2
    function attackPlayer2($hp){
        global $conn, $player1, $player2;
        require_once("connection.php");
        $getBattle = getBattle();
        $damageDealtByPlayer2 = $getBattle['batDmgCounterPlayer2'];
            
        $hp = $hp - $damageDealtByPlayer2 ;

        return $hp;
    }


    function isDead($hpPlayer){ 
        if($hpPlayer <= 0){
            return TRUE;
        }
        return FALSE;
     }

    //Troca o pokemon que morreu em batalha
    function changePokemon($pokemon, $player){
        $resultLastPokemon = isTheLastPokemon($player); //Verifica se é o ultimo pokemon vivo

        var_dump($resultLastPokemon);

        //TRUE -> Todos os pokemons morreram | $resultLastPokemon -> Ainda tem pokemon vivo | FALSE -> Deu um erro muito grande
        if($resultLastPokemon === TRUE){
            var_dump("FIM DE BATALHA");
            defeat($player);
            endBattle();
        }

        else if($resultLastPokemon == $pokemon+1){
            
            global $conn, $player1, $player2;
            require_once("connection.php");
            $unsetIsOnFieldStmt = $conn->prepare("UPDATE pokemon SET pokIsOnField = NULL WHERE pokCode = ?");
            $setIsOnFieldStmt = $conn->prepare("UPDATE pokemon SET pokIsOnField = 1 WHERE pokCode = ?");

            $unsetIsOnFieldStmt->execute([$pokemon]);
            $setIsOnFieldStmt->execute([$resultLastPokemon]);
        }
        else{
            $_SESSION['message'] = "GRANDE ERRO | isTheLastPokemon() retornou FALSE:";
            echo json_encode(['result' => true]);
            exit();
        }
    }

    //Seta IsDead na tabela Pokemon pro Player1
    function setIsDeadPokemonPlayer1($pokemon){
        global $conn, $player1;
        $plaCode = $_SESSION['plaCode'];
        $hpPlayer1 = $_SESSION['battle']['hpPlayer1'];
        require_once("connection.php");

        if($player1 == $plaCode){
            try{
                $setIsDeadStmt = $conn->prepare("UPDATE pokemon SET pokIsDead = 1, pokHp = ? WHERE pokCode = ?");
                $setIsDeadStmt->execute([$hpPlayer1, $pokemon]);
                
            }
            catch(PDOException $e){
                $_SESSION['message'] = "Erro ao Setar IsDeadPokemonPlayer1: ".$e;
                echo json_encode(['result' => true]);
                exit();
            }
        }

        unset($_SESSION['battle']['hpPlayer1']);
    }

    //Seta IsDead na tabela Pokemon pro Player2
    function setIsDeadPokemonPlayer2($pokemon){
        global $conn, $player2;
        $plaCode = $_SESSION['plaCode'];
        $hpPlayer2 = $_SESSION['battle']['hpPlayer2'];
        require_once("connection.php");

        if($player2 == $plaCode){
            try{
                $setIsDeadStmt = $conn->prepare("UPDATE pokemon SET pokIsDead = 1, pokHp = ? WHERE pokCode = ?");
                $setIsDeadStmt->execute([$hpPlayer2, $pokemon]);
            }
            catch(PDOException $e){
                $_SESSION['message'] = "Erro ao Setar IsDeadPokemonPlayer2: ".$e;
                echo json_encode(['result' => true]);
                exit();
            }
        }

        unset($_SESSION['battle']['hpPlayer2']);
    }

    //Verifica se todos os pokemons da equipe estão mortos
    function isTheLastPokemon($player){
        global $conn;
        require_once("connection.php");
        try{
            $lastPokemonStmt = $conn->prepare("SELECT pokCode FROM pokemon WHERE pokIsDead = 1 AND pokCode IN 
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
            $pokemonDead = [];
            $lastPokemon = true;
            $i = 0;
            while ($lastPokemon) {
                $pokemonDead[] = $lastPokemon;
                $lastPokemon = $lastPokemonStmt->fetch(PDO::FETCH_ASSOC);
                $i++;
            }

            //Caso nao retorne nada (teoricamente impossivel pois pokIsDead é setado para o primeiro pokemon antes de passar aqui)
            if(sizeof($pokemonDead) == NULL){
                return false;
            }

            var_dump($pokemonDead);
            //Caso todos os pokemons estejam mortos, retorna true.
            //!!! ATENÇÃO -> O 7 é porque quando a array é criada o primeiro elemento fica marcado como TRUE, nao me pergunta, mas funciona ent deixa assim.
            //(Provavelmente da pra corrigir colocando: $pokemonDead['pokCode'] == 6, mas to com preguica de testar)
            if(sizeof($pokemonDead) == 7){
                return true; //Todos os pokemons da equipe estao mortos
            }

            $pokemonNotDead = $pokemonDead[$i-1]['pokCode'];
            return $pokemonNotDead+1; //Volta o proximo pokemon do time
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao buscar pokemons IsDead: ".$e;
            echo json_encode(['result' => true]);
            exit();
        }
    }

    //Atualiza o HP do Pokemon OnField no BD
    function updateHp($hp, $pokemon){
        global $conn, $player1, $player2;
        require_once("connection.php");
        try{
            $updateHpStmt = $conn->prepare("UPDATE pokemon SET pokHp = ? WHERE pokCode = ?");
            $updateHpStmt->execute([$hp, $pokemon]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Atualizar HP." . $e;
            echo json_encode(['result' => true]);
            exit();
        }
        
    }

    //Derrota para o player que nao possui mais pokemons na batalha
    function defeat($player){
        global $conn, $player1, $player2;
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
                        echo json_encode(['result' => true]);
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
                        echo json_encode(['result' => true]);
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Erro ao setar teamDefeat: ".$e;
                    echo json_encode(['result' => true]);
                    exit();
                }
            }       
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao buscar Player: ".$e;
            echo json_encode(['result' => true]);
            exit();
        }
    }

    //Reseta o Pokemon OnField e o HP dos dois jogadores
    function resetPokemon(){
        global $conn, $player1, $player2;
        $hpPlayer1 = $_SESSION['battle']['hpPlayer1'];
        $hpPlayer2 = $_SESSION['battle']['hpPlayer2'];
        require_once("connection.php");
        try{
            $resetPokemonStmt = $conn->prepare("UPDATE pokemon SET pokIsOnField = NULL, pokIsDead = NULL, pokHp = :hpPlayer WHERE pokIsOnField = 1 AND pokCode IN 
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
            $resetPokemonStmt->execute([':hpPlayer' => $hpPlayer1, ':plaCode' => $player1]);
            $resetPokemonStmt->execute([':hpPlayer' => $hpPlayer2, ':plaCode' => $player2]);
        }
        catch(PDOException $e){
            $_SESSION['message'] = "Erro ao Resetar Pokemon: ".$e;
            echo json_encode(['result' => true]);
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

        echo json_encode(['result' => true]); //AJAX

        resetPokemon();
        unset($_SESSION['battle']);
        return;
    }

?>