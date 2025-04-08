<?php

class TeamModel {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getTeamsByPlayer($plaCode) {
        $sql = "SELECT * FROM team WHERE teaPlaCode = :plaCode";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':plaCode', $plaCode, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPokemonByCode($pokeCode) {
        $sql = "SELECT * FROM pokemon WHERE pokCode = :pokeCode";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':pokeCode', $pokeCode, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}