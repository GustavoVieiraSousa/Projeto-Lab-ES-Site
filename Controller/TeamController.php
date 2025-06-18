<?php

require_once '../Model/php/TeamModel.php';

class TeamController {
    private $teamModel;

    public function __construct($dbConnection) {
        $this->teamModel = new TeamModel($dbConnection);
    }

    public function getTeams($plaCode) {
        $teams = $this->teamModel->getTeamsByPlayer($plaCode);

        // Para cada time, busque os dados dos Pokémon
        foreach ($teams as &$team) {
            for ($i = 1; $i <= 6; $i++) {
                $pokeCode = $team["teaPokCode{$i}"];
                $team["pokemon{$i}"] = $this->teamModel->getPokemonByCode($pokeCode);
            }
        }

        return $teams;
    }
}