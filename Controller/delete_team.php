<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json'); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['teamId'])) {
        error_log('ID do time não fornecido.');
        echo json_encode(['error' => 'ID do time não fornecido.']);
        exit();
    }

    $teamId = $data['teamId'];
    $plaCode = $_SESSION['plaCode']; 
    error_log('Recebido teamId: ' . $teamId . ', plaCode: ' . $plaCode);

    try {
        $conn->beginTransaction();


        $getPokemonCodesSql = "SELECT teaPokCode1, teaPokCode2, teaPokCode3, teaPokCode4, teaPokCode5, teaPokCode6 
                               FROM team 
                               WHERE teaCode = :teamId AND teaPlaCode = :plaCode";
        $getPokemonCodesStmt = $conn->prepare($getPokemonCodesSql);
        $getPokemonCodesStmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $getPokemonCodesStmt->bindParam(':plaCode', $plaCode, PDO::PARAM_INT);
        $getPokemonCodesStmt->execute();
        $pokemonCodes = $getPokemonCodesStmt->fetch(PDO::FETCH_ASSOC);

        
        $deleteTeamSql = "DELETE FROM team WHERE teaCode = :teamId AND teaPlaCode = :plaCode";
        $deleteTeamStmt = $conn->prepare($deleteTeamSql);
        $deleteTeamStmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $deleteTeamStmt->bindParam(':plaCode', $plaCode, PDO::PARAM_INT);
        $deleteTeamStmt->execute();
        error_log('Time excluído com sucesso.');

       
        if ($pokemonCodes) {
            $pokemonCodes = array_filter($pokemonCodes); 
            if (!empty($pokemonCodes)) {
                $placeholders = implode(',', array_fill(0, count($pokemonCodes), '?'));
                $deletePokemonSql = "DELETE FROM pokemon WHERE pokCode IN ($placeholders)";
                $deletePokemonStmt = $conn->prepare($deletePokemonSql);
                $deletePokemonStmt->execute(array_values($pokemonCodes));
                error_log('Pokémon associados ao time excluídos com sucesso.');
            }
        }

        $conn->commit();

        echo json_encode(['success' => 'Time e Pokémon associados excluídos com sucesso!']);
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log('Erro ao excluir time ou Pokémon: ' . $e->getMessage());
        echo json_encode(['error' => 'Erro ao excluir time ou Pokémon.']);
    }
}