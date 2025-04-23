<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styles.css">
    <title>Salas</title>
</head>
<body>

    <?php
        session_start();
        if(!isset($_SESSION['user']) == true){
            header('Location: login.php');
            exit();
        }

        $roomCode = $_SESSION['roomCode'];
    ?>

    <div id="app">
        <nav class="navbar">
            <div class="container">
                <a href="/" class="logo">PokéParty</a>
                <div class="nav-links">
                    <a href="index.php">Home</a> 
                    <a href="pokedex.php" class="active">Pokédex</a>
                    <a href="teams.php">Times</a>
                    <!-- <a href="battle.php">Batalha</a> -->
                    <a href="profile.php">Perfil</a>
                </div>
                <div class="search-bar">
                    <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
                </div>
            </div> 
        </nav>
    </div>

    <br><br><br><br>

    <div>
        <h1>Sala de Batalha</h1>
        <p><?php echo $roomCodeVar = (isset($roomCode)) ? $roomCode : "Nenhuma Sala" ?></p>
        <form method="POST" action="../Controller/addRoom.php">
            <input type="submit" name="addRoom"/>
        </form>
    </div>

    <?php unset($_SESSION['roomCode']) ?>

</body>
</html>