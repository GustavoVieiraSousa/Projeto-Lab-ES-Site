<?php
    session_start();
    require_once '../Controller/connection.php';
    require_once '../Controller/TeamController.php';

    if (!isset($_SESSION['user']) == true) {
        header('Location: login.php');
        exit();
    }

    $plaCode = $_SESSION['plaCode'];
    $teamController = new TeamController($conn);
    $teams = $teamController->getTeams($plaCode);

    echo "ID do jogador: " . $_SESSION['plaCode'];

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
            <a href="/">Home</a> 
            <a href="pokedex.php" >Pokédex</a>
            <a href="teams.php" class="active">Times</a>
            <a href="battle.php">Batalha</a>
          </div>
          <div class="search-bar">
            <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
          </div>
        </div>
      </nav>
</div>

<br>
<br>
<br>
<br>


<div class="teams-container">
    <?php foreach ($teams as $team): ?>
        <div class="team-grid" id="team-<?= $team['teaCode'] ?>">
            <h3><?= htmlspecialchars($team['teaName']) ?></h3>
            <div class="pokemon-grid">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <?php
                    // Obtenha os dados do Pokémon
                    $pokemon = $team["pokemon{$i}"];
                    $pokeId = $pokemon['pokId']; // Identificador da espécie (1 a 151)
                    ?>
                    <div class="pokemon-slot pokemon-<?= $pokeId ?>" 
                         data-team-id="<?= $team['teaCode'] ?>" 
                         data-pokemon-id="<?= $pokeId ?>">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $pokeId ?>.png" 
                             alt="Pokémon <?= $pokeId ?>">
                    </div>
                <?php endfor; ?>
            </div>
            <div class="action-buttons">
                <button class="edit">Editar Time</button>
                <button class="delete">Excluir Time</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="../Model/js/TeamShow.js"></script>
</body>
</html>
