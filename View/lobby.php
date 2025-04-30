<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styleRoom.css">
    <link rel="stylesheet" href="Style/styles.css">
    <title>Lobby</title>
</head>
<body>

    <?php
    session_start();
    require_once '../Controller/connection.php';
    require_once '../Controller/TeamController.php';

    if (!isset($_SESSION['user']) || !$_SESSION['user']) {
        header('Location: login.php');
        exit();
    }

    if (!isset($_SESSION['roomCode'])){
        header('Location: roomList.php');
        exit();
    }
    
    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message']); // Limpa a mensagem depois de mostrar
    }

    $plaCode = $_SESSION['plaCode'];
    $teamController = new TeamController($conn);
    $teams = $teamController->getTeams($plaCode);
    ?>

    <div id="app">
        <nav class="navbar">
            <div class="container">
                <a href="/" class="logo">PokéParty</a>
                <div class="nav-links">
                    <a href="index.php">Home</a> 
                    <a href="pokedex.php">Pokédex</a>
                    <a href="teams.php">Times</a>
                    <a href="roomList.php" class="active">Salas</a>
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

    <div class="content-search">
        <h1>Escolha seu time:</h1>
        
        <updatelist>
            <!-- BOTÃO QUE ENVIA O USUARIO PARA A TELA DE TIMES PARA CRIAR SEU TIME -->
            <div class="create-team">
                <a class="go" href="teams.php" class="create-team-button">Ir para tela de Times</a>
            </div>
            <form class="create-team" method="POST" action="../Controller/deleteRoom.php">
                <input class="exit" type="submit" name="roomList" value="Sair da Sala"/>
            </form>
        </updatelist>
    </div>

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
                                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $pokeId ?>.png" alt="Pokémon <?= $pokeId ?>">
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="action-buttons">
                        <button class="select" id="select-team-button" data-team-id="<?= $team['teaCode'] ?>">Selecionar Time</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-teams">Nenhum time encontrado. Crie um novo time!</p>
        <?php endif; ?>
    </div>

    <!-- <lobby>
        <main>
            <h1>Informações da Sala Atual</h1>
            <info class="info-room">
                <info class="info-player1">
                    <p>ID da Sala:</strong> <?php echo "xxx"; ?></p>
                    <p>Player 1:</strong> <?php echo "xxx"; ?></p>
                    
                </info>
                <info class="info-player2">
                    <p>ID da Sala:</strong> <?php echo "xxx"; ?></p>
                    <p>Player 2:</strong> <?php echo "xxx"; ?></p>

                </info>
            </info>
        </main>
    </lobby> -->
    <script src="../Model/js/room/lobbyToBattleTeam.js"></script>
</body>
</html>