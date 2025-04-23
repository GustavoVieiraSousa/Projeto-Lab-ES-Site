<?php
session_start();
require_once '../Controller/connection.php';
require_once '../Controller/TeamController.php';

if (!isset($_SESSION['user']) || !$_SESSION['user']) {
    header('Location: login.php');
    exit();
}

$plaCode = $_SESSION['plaCode'];
$teamController = new TeamController($conn);
$teams = $teamController->getTeams($plaCode);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PokéParty</title>
    <link rel="stylesheet" href="Style/styles.css">
    <meta name="description" content="PokéParty - Pokedex e Times Pokemon" />
</head>

<body>
    <div id="app">
        <nav class="navbar">
            <div class="container">
                <a href="/" class="logo">PokéParty</a>
                <div class="nav-links">
                    <a href="index.php">Home</a> 
                    <a href="pokedex.php">Pokédex</a>
                    <a href="teams.php" class="active">Times</a>
                    <!-- <a href="battle.php">Batalha</a> -->
                    <a href="profile.php">Perfil</a>
                </div>
                <div class="place-holder">
                    <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
                </div>
            </div>
        </nav>
    </div>

    <br><br><br><br>

    <div class="teams-container">
        <?php if (!empty($teams)): ?>
            <?php foreach ($teams as $team): ?>
                <div class="team-grid" id="team-<?= $team['teaCode'] ?>">
                    <h3><?= htmlspecialchars($team['teaName']) ?></h3>
                    <div class="pokemon-grid">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <?php
                            $pokemon = $team["pokemon{$i}"];
                            if ($pokemon): 
                                $pokeId = $pokemon['pokId'];
                            ?>
                                <div class="pokemon-slot pokemon-<?= $pokeId ?>" 
                                     data-team-id="<?= $team['teaCode'] ?>" 
                                     data-pokemon-id="<?= $pokeId ?>">
                                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $pokeId ?>.png" 
                                         alt="Pokémon <?= $pokeId ?>">
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="action-buttons">
                        <button class="edit" data-team-id="<?= $team['teaCode'] ?>">Editar Time</button>
                        <button class="delete" data-team-id="<?= $team['teaCode'] ?>">Excluir Time</button>
                    </div>  
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-teams">Nenhum time encontrado. Crie um novo time!</p>
        <?php endif; ?>

        <!-- Botão para adicionar um novo time -->
        <div class="add-team">
            <button id="add-team-button">+</button>
        </div>
    </div>

    <!-- Modal para criação de time -->
    <div id="create-team-modal" class="modal team-modal hidden">
        <div class="modal-content">
            <h2 class="cnt-h2">Criar Novo Time</h2>
            <form id="create-team-form">
                <div class="team-name">
                    <label for="team-name-input">Nome do Time:</label>
                    <input type="text" id="team-name-input" name="teamName" placeholder="Digite o nome do time" required>
                </div>

                <div id="pokemon-selection">
                    <div class="team-pokemon-grid">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <div class="team-pokemon-slot" id="slot-<?= $i ?>">
                                <p>Slot <?= $i ?></p>
                                <button type="button" class="team-select-pokemon" data-slot="<?= $i ?>">Selecionar Pokémon</button>
                                <div class="team-selected-pokemon" id="selected-pokemon-<?= $i ?>"></div>
                                <div class="team-attack-row">
                                    <?php for ($j = 1; $j <= 2; $j++): ?>
                                        <button type="button" class="team-select-attack" data-slot="<?= $i ?>" data-attack="<?= $j ?>">Selecionar Ataque</button>
                                        <div class="team-selected-attack" id="selected-attack-<?= $i ?>-<?= $j ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="team-attack-row">
                                    <?php for ($j = 3; $j <= 4; $j++): ?>
                                        <button type="button" class="team-select-attack" data-slot="<?= $i ?>" data-attack="<?= $j ?>">Selecionar Ataque</button>
                                        <div class="team-selected-attack" id="selected-attack-<?= $i ?>-<?= $j ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" id="create-team-button" data-mode="create" disabled>Criar Time</button>
                    <span class="close-button">&times;</span>
                    <button type="button" id="cancel-team-button">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup para selecionar Pokémon -->
    <div id="pokemon-popup" class="popup hidden">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <h2>Selecione um Pokémon</h2>
            <div id="pokemon-list" class="scrollable-list">
                <!-- A lista de Pokémon será carregada dinamicamente -->
            </div>
        </div>
    </div>

    <!-- Popup para selecionar Ataques -->
    <div id="attack-popup" class="popup hidden">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <h2>Selecione um Ataque</h2>
            <div id="attack-list" class="scrollable-list">
                <!-- A lista de ataques será carregada dinamicamente -->
            </div>
        </div>
    </div>

    <script src="../Model/js/api.js"></script>
    <script src="../Model/js/TeamShow.js"></script>
    <script src="../Model/js/createTeam.js"></script>
    <script src="../Model/js/editTeam.js"></script>
    <script src="../Model/js/deleteTeam.js"></script>
</body>
</html>