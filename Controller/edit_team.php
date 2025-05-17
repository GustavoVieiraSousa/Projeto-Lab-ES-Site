<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $teamId = $data['teamId'] ?? null;
    $teamName = $data['teamName'] ?? null;
    $pokemons = $data['pokemons'] ?? [];
    $attacks = $data['attacks'] ?? [];

    if (!$teamId || !$teamName || count($pokemons) !== 6 || count($attacks) !== 6) {
        echo json_encode(['error' => 'Dados inválidos.']);
        exit();
    }

    try {
        $conn->beginTransaction();

        // Atualiza o nome do time
        $updateTeamSql = "UPDATE team SET teaName = :teamName WHERE teaCode = :teamId";
        $updateTeamStmt = $conn->prepare($updateTeamSql);
        $updateTeamStmt->bindParam(':teamName', $teamName, PDO::PARAM_STR);
        $updateTeamStmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $updateTeamStmt->execute();

        // Atualiza os Pokémon e ataques
        for ($i = 0; $i < 6; $i++) {
            $pokCodeColumn = "teaPokCode" . ($i + 1);
            $pokemon = $pokemons[$i];

            // Verifica se o Pokémon já existe no time
            $getPokemonSql = "SELECT $pokCodeColumn FROM team WHERE teaCode = :teamId";
            $getPokemonStmt = $conn->prepare($getPokemonSql);
            $getPokemonStmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
            $getPokemonStmt->execute();
            $pokCode = $getPokemonStmt->fetchColumn();

            if ($pokCode) {
                // Atualiza o Pokémon existente
                $updatePokemonSql = "UPDATE pokemon SET pokId = :pokId, pokAtk1 = :atk1, pokAtk2 = :atk2, pokAtk3 = :atk3, pokAtk4 = :atk4, 
                pokBasicAttack = :pokBasicAttack, pokSpecialAttack = :pokSpecialAttack, pokBasicDefense = :pokBasicDefense,
                pokSpecialDefense = :pokSpecialDefense, pokHp = :pokHp, pokSpeed = :pokSpeed WHERE pokCode = :pokCode";
                $updatePokemonStmt = $conn->prepare($updatePokemonSql);
                $updatePokemonStmt->execute([
                    ':pokId' => $pokemon['pokId'],
                    ':atk1' => $attacks[$i][0],
                    ':atk2' => $attacks[$i][1],
                    ':atk3' => $attacks[$i][2],
                    ':atk4' => $attacks[$i][3],
                    ':pokBasicAttack' => $pokemon['pokBasicAttack'],
                    ':pokSpecialAttack' => $pokemon['pokSpecialAttack'],
                    ':pokBasicDefense' => $pokemon['pokBasicDefense'],
                    ':pokSpecialDefense' => $pokemon['pokSpecialDefense'],
                    ':pokHp' => $pokemon['pokHp'],
                    ':pokSpeed' => $pokemon['pokSpeed'],
                    ':pokCode' => $pokCode,
                ]);
            } else {
                // Insere um novo Pokémon se não existir
                $insertPokemonSql = "INSERT INTO pokemon (pokId, pokAtk1, pokAtk2, pokAtk3, pokAtk4, pokBasicAttack, pokSpecialAttack, pokBasicDefense, pokSpecialDefense, pokHp, pokSpeed) 
                VALUES (:pokId, :atk1, :atk2, :atk3, :atk4, :pokBasicAttack, :pokSpecialAttack, :pokBasicDefense, :pokSpecialDefense, :pokHp, :pokSpeed)";
                $insertPokemonStmt = $conn->prepare($insertPokemonSql);
                $insertPokemonStmt->execute([
                    ':pokId' => $pokemons[$i],
                    ':atk1' => $attacks[$i][0],
                    ':atk2' => $attacks[$i][1],
                    ':atk3' => $attacks[$i][2],
                    ':atk4' => $attacks[$i][3],
                    ':pokBasicAttack' => $pokemon['pokBasicAttack'],
                    ':pokSpecialAttack' => $pokemon['pokSpecialAttack'],
                    ':pokBasicDefense' => $pokemon['pokBasicDefense'],
                    ':pokSpecialDefense' => $pokemon['pokSpecialDefense'],
                    ':pokHp' => $pokemon['pokHp'],
                    ':pokSpeed' => $pokemon['pokSpeed'],
                ]);

                // Atualiza o código do Pokémon no time
                $newPokCode = $conn->lastInsertId();
                $updateTeamPokemonSql = "UPDATE team SET $pokCodeColumn = :pokCode WHERE teaCode = :teamId";
                $updateTeamPokemonStmt = $conn->prepare($updateTeamPokemonSql);
                $updateTeamPokemonStmt->execute([
                    ':pokCode' => $newPokCode,
                    ':teamId' => $teamId,
                ]);
            }
        }

        $conn->commit();
        echo json_encode(['success' => 'Time atualizado com sucesso!']);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['error' => 'Erro ao atualizar o time: ' . $e->getMessage()]);
    }
}