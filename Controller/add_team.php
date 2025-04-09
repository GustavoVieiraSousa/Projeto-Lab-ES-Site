<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lê os dados JSON enviados pelo JavaScript
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Verifica se os dados necessários foram enviados
    if (!isset($data['teamName'], $data['pokemons'], $data['attacks'])) {
        error_log('Dados incompletos enviados: ' . print_r($data, true));
        echo json_encode(['error' => 'Dados incompletos enviados.']);
        exit();
    }

    $plaCode = $_SESSION['plaCode']; 
    $teamName = $data['teamName']; 
    $pokemons = $data['pokemons']; 
    $attacks = $data['attacks']; 

    // Verifica se exatamente 6 Pokémon foram enviados
    if (count($pokemons) !== 6 || count($attacks) !== 6) {
        error_log('Número incorreto de Pokémon ou ataques enviados.');
        echo json_encode(['error' => 'Você deve selecionar exatamente 6 Pokémon e 4 ataques para cada um.']);
        exit();
    }

    try {
        // Inicia uma transação para garantir que tudo seja salvo corretamente
        $conn->beginTransaction();

        // Array para armazenar os pokCode gerados
        $pokCodes = [];

        // Insere cada Pokémon na tabela `pokemon`
        $pokemonSql = "INSERT INTO pokemon (pokId, pokAtk1, pokAtk2, pokAtk3, pokAtk4)
                       VALUES (:pokId, :pokAtk1, :pokAtk2, :pokAtk3, :pokAtk4)";
        $pokemonStmt = $conn->prepare($pokemonSql);

        foreach ($pokemons as $index => $pokId) {
            $pokemonStmt->execute([
                ':pokId' => $pokId,
                ':pokAtk1' => $attacks[$index][0],
                ':pokAtk2' => $attacks[$index][1],
                ':pokAtk3' => $attacks[$index][2],
                ':pokAtk4' => $attacks[$index][3],
            ]);

            // Obtém o pokCode gerado
            $pokCodes[] = $conn->lastInsertId();
        }

        // Insere o time na tabela `team`
        $teamSql = "INSERT INTO team (teaName, teaPlaCode, teaPokCode1, teaPokCode2, teaPokCode3, teaPokCode4, teaPokCode5, teaPokCode6)
                    VALUES (:teamName, :plaCode, :pok1, :pok2, :pok3, :pok4, :pok5, :pok6)";
        $teamStmt = $conn->prepare($teamSql);
        $teamStmt->execute([
            ':teamName' => $teamName,
            ':plaCode' => $plaCode,
            ':pok1' => $pokCodes[0],
            ':pok2' => $pokCodes[1],
            ':pok3' => $pokCodes[2],
            ':pok4' => $pokCodes[3],
            ':pok5' => $pokCodes[4],
            ':pok6' => $pokCodes[5],
        ]);

        // Confirma a transação
        $conn->commit();

        echo json_encode(['success' => 'Time criado com sucesso!']);
    } catch (PDOException $e) {
        // Reverte a transação em caso de erro
        $conn->rollBack();
        error_log('Erro ao criar time: ' . $e->getMessage()); 
        echo json_encode(['error' => 'Erro ao criar time: ' . $e->getMessage()]);
    }
}