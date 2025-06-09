<?php
require_once("connection.php");
session_start();

$player1 = $_SESSION['battle']['player1'];
$player2 = $_SESSION['battle']['player2'];

try{
    $getPokemonsStmt = $conn->prepare("SELECT * FROM pokemon WHERE pokIsOnField = 1 AND pokCode IN 
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

    //get Pokemon do Player 1
    $getPokemonsStmt->execute([':plaCode' => $player1]);
    $getPokemons = $getPokemonsStmt->fetch(PDO::FETCH_ASSOC);

    //get Pokemon do Player 2
    $getPokemonsStmt->execute([':plaCode' => $player2]);
    $getPokemons2 = $getPokemonsStmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e){
    $_SESSION['message'] = "Erro buscar pokemons";
    echo json_encode(['res' => false, 'error' => 'Database error']);
    exit();
}

//-----------------------------------------------------------------------------

//armazenando dados Pokemon do Player 1
$pokIdP1 = $getPokemons['pokId'];
$moveId1P1 = $getPokemons['pokAtk1'];
$moveId2P1 = $getPokemons['pokAtk2'];
$moveId3P1 = $getPokemons['pokAtk3'];
$moveId4P1 = $getPokemons['pokAtk4'];

//armazenando dados Pokemon do Player 2
$pokIdP2 = $getPokemons2['pokId'];
$moveId1P2 = $getPokemons2['pokAtk1'];
$moveId2P2 = $getPokemons2['pokAtk2'];
$moveId3P2 = $getPokemons2['pokAtk3'];
$moveId4P2 = $getPokemons2['pokAtk4'];

//-----------------------------------------------------------------------------

// Busca os dados do ataque na PokéAPI - Pokemon do Player 1
$pokeApiUrl1P1 = "https://pokeapi.co/api/v2/move/$moveId1P1";
$moveJson1P1 = file_get_contents($pokeApiUrl1P1);
$moveData1P1 = json_decode($moveJson1P1, true);

$pokeApiUrl2P1 = "https://pokeapi.co/api/v2/move/$moveId2P1";
$moveJson2P1 = file_get_contents($pokeApiUrl2P1);
$moveData2P1 = json_decode($moveJson2P1, true);

$pokeApiUrl3P1 = "https://pokeapi.co/api/v2/move/$moveId3P1";
$moveJson3P1 = file_get_contents($pokeApiUrl3P1);
$moveData3P1 = json_decode($moveJson3P1, true);

$pokeApiUrl4P1 = "https://pokeapi.co/api/v2/move/$moveId4P1";
$moveJson4P1 = file_get_contents($pokeApiUrl4P1);
$moveData4P1 = json_decode($moveJson4P1, true);

// Busca os dados do ataque na PokéAPI - Pokemon do Player 2
$pokeApiUrl1P2 = "https://pokeapi.co/api/v2/move/$moveId1P2";
$moveJson1P2 = file_get_contents($pokeApiUrl1P2);
$moveData1P2 = json_decode($moveJson1P2, true);

$pokeApiUrl2P2 = "https://pokeapi.co/api/v2/move/$moveId2P2";
$moveJson2P2 = file_get_contents($pokeApiUrl2P2);
$moveData2P2 = json_decode($moveJson2P2, true);

$pokeApiUrl3P2 = "https://pokeapi.co/api/v2/move/$moveId3P2";
$moveJson3P2 = file_get_contents($pokeApiUrl3P2);
$moveData3P2 = json_decode($moveJson3P2, true);

$pokeApiUrl4P2 = "https://pokeapi.co/api/v2/move/$moveId4P2";
$moveJson4P2 = file_get_contents($pokeApiUrl4P2);
$moveData4P2 = json_decode($moveJson4P2, true);

//-----------------------------------------------------------------------------

// Pega o valor do "power" e "name" da api e armazena - Pokemon do Player 1
$power1P1 = $moveData1P1['power'] ?? null; //se retornar sem valor, seta para null
$atkName1P1 = $moveData1P1['name'] ?? null;

$power2P1 = $moveData2P1['power'] ?? null;
$atkName2P1 = $moveData2P1['name'] ?? null;

$power3P1 = $moveData3P1['power'] ?? null;
$atkName3P1 = $moveData3P1['name'] ?? null;

$power4P1 = $moveData4P1['power'] ?? null;
$atkName4P1 = $moveData4P1['name'] ?? null;

// Pega o valor do "power" e "name" da api e armazena - Pokemon do Player 2
$power1P2 = $moveData1P2['power'] ?? null; //se retornar sem valor, seta para null
$atkName1P2 = $moveData1P2['name'] ?? null;

$power2P2 = $moveData2P2['power'] ?? null;
$atkName2P2 = $moveData2P2['name'] ?? null;

$power3P2 = $moveData3P2['power'] ?? null;
$atkName3P2 = $moveData3P2['name'] ?? null;

$power4P2 = $moveData4P2['power'] ?? null;
$atkName4P2 = $moveData4P2['name'] ?? null;

//-----------------------------------------------------------------------------

// var_dump($power1P1, $power2P1, $power3P1, $power4P1);
// var_dump($power1P2, $power2P2, $power3P2, $power4P2);

//-----------------------------------------------------------------------------

// //Verificando se todos tem valor - Pokemon do Player 1
// if($power1P1 == NULL || $power2P1 == NULL || $power3P1 == NULL || $power4P1 == NULL){
//     echo json_encode(['res' => false, 'error' => 'Database power Player1 error']);
// }
// if($atkName1P1 == NULL || $atkName2P1 == NULL || $atkName3P1 == NULL || $atkName4P1 == NULL){
//     echo json_encode(['res' => false, 'error' => 'Database atkName Player1 error']);
// }

// //Verificando se todos tem valor - Pokemon do Player 2
// if($power1P2 == NULL || $power2P2 == NULL || $power3P2 == NULL || $power4P2 == NULL){
//     echo json_encode(['res' => false, 'error' => 'Database power Player2 error']);
//     exit();
// }
// if($atkName1P2 == NULL || $atkName2P2 == NULL || $atkName3P2 == NULL || $atkName4P2 == NULL){
//     echo json_encode(['res' => false, 'error' => 'Database atkName Player2 error']);
//     exit();
// }

//Dados Pokemon do Player 1
$_SESSION['battle']['pokemon1']['power1'] = $power1P1;
$_SESSION['battle']['pokemon1']['power2'] = $power2P1;
$_SESSION['battle']['pokemon1']['power3'] = $power3P1;
$_SESSION['battle']['pokemon1']['power4'] = $power4P1;
$_SESSION['battle']['pokemon1']['atkName1'] = $atkName1P1;
$_SESSION['battle']['pokemon1']['atkName2'] = $atkName2P1;
$_SESSION['battle']['pokemon1']['atkName3'] = $atkName3P1;
$_SESSION['battle']['pokemon1']['atkName4'] = $atkName4P1;

//Dados Pokemon do Player 2
$_SESSION['battle']['pokemon2']['power1'] = $power1P2;
$_SESSION['battle']['pokemon2']['power2'] = $power2P2;
$_SESSION['battle']['pokemon2']['power3'] = $power3P2;
$_SESSION['battle']['pokemon2']['power4'] = $power4P2;
$_SESSION['battle']['pokemon2']['atkName1'] = $atkName1P2;
$_SESSION['battle']['pokemon2']['atkName2'] = $atkName2P2;
$_SESSION['battle']['pokemon2']['atkName3'] = $atkName3P2;
$_SESSION['battle']['pokemon2']['atkName4'] = $atkName4P2;

echo json_encode(['res' => true,
    'power1P1' => $power1P1,
    'power2P1' => $power2P1,
    'power3P1' => $power3P1,
    'power4P1' => $power4P1,
    'atkName1P1' => $atkName1P1,
    'atkName2P1' => $atkName2P1,
    'atkName3P1' => $atkName3P1,
    'atkName4P1' => $atkName4P1,

    'power1P2' => $power1P2,
    'power2P2' => $power2P2,
    'power3P2' => $power3P2,
    'power4P2' => $power4P2,
    'atkName1P2' => $atkName1P2,
    'atkName2P2' => $atkName2P2,
    'atkName3P2' => $atkName3P2,
    'atkName4P2' => $atkName4P2,
]);