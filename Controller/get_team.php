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
                    $pokemonSql = "SELECT pokCode, pokId, pokAtk1, pokAtk2, pokAtk3, pokAtk4 FROM pokemon WHERE pokCode = :pokCode";
                    $pokemonStmt = $conn->prepare($pokemonSql);
                    $pokemonStmt->bindParam(':pokCode', $pokCode, PDO::PARAM_INT);
                    $pokemonStmt->execute();
                    $pokemon = $pokemonStmt->fetch(PDO::FETCH_ASSOC);

                    if ($pokemon) {
                        $pokemons[] = $pokemon; // Adiciona o pokCode, pokId e ataques ao array
                    }
                } else {
                    $pokemons[] = null; // Slot vazio
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