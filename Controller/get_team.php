<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Inicializa as variáveis
$team = null;
$pokemons = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $teamId = $_GET['teamId'] ?? null;

    if (!$teamId) {
        echo json_encode(['error' => 'ID do time não fornecido.']);
        exit();
    }

    try {
        // Busca o time pelo ID
        $sql = "SELECT * FROM team WHERE teaCode = :teamId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            // Busca os Pokémon associados ao time
            for ($i = 1; $i <= 6; $i++) {
                $pokCode = $team["teaPokCode{$i}"];
                if ($pokCode) {
                    $pokemonSql = "SELECT * FROM pokemon WHERE pokCode = :pokCode";
                    $pokemonStmt = $conn->prepare($pokemonSql);
                    $pokemonStmt->bindParam(':pokCode', $pokCode, PDO::PARAM_INT);
                    $pokemonStmt->execute();
                    $pokemon = $pokemonStmt->fetch(PDO::FETCH_ASSOC);

                    if ($pokemon) {
                        $formatedPokemon = [
                            'pokId' => $pokemon['pokId'],
                            'pokAtk1' => $pokemon['pokAtk1'],
                            'pokAtk2' => $pokemon['pokAtk2'],
                            'pokAtk3' => $pokemon['pokAtk3'],
                            'pokAtk4' => $pokemon['pokAtk4'],
                            'stats' => [
                                'pokBasicAttack' => $pokemon['pokBasicAttack'],
                                'pokSpecialAttack' => $pokemon['pokSpecialAttack'],
                                'pokBasicDefense' => $pokemon['pokBasicDefense'],
                                'pokSpecialDefense' => $pokemon['pokSpecialDefense'],
                                'pokHp' => $pokemon['pokHp'],
                                'pokSpeed' => $pokemon['pokSpeed'],
                            ],                            
                        ];
                        $pokemons[] = $formatedPokemon; 
                    }
                } else {
                    $pokemons[] = null; 
                }
            }

            // Retorna os dados do time e dos Pokémon
            echo json_encode([
                'success' => true,
                'team' => [
                    'teaName' => $team['teaName'],
                    'pokemons' => $pokemons,
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Time não encontrado.']);
        }
    } catch (PDOException $e) {
        error_log('Erro ao buscar o time: ' . $e->getMessage());
        echo json_encode(['error' => 'Erro ao buscar o time.']);
    }
}