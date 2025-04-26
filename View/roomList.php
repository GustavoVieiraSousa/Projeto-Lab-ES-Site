<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styles.css">
    <title>Salas</title>
</head>
    <?php
        session_start();

        if(!isset($_SESSION['user'])){
            header('Location: login.php');
            exit();
        }

        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
            unset($_SESSION['message']); // Limpa a mensagem depois de mostrar
        }

        $roomCode = (isset($_SESSION['roomCode'])) ? $_SESSION['roomCode'] : "Nenhuma Sala";
        $plaCode = (isset($_SESSION['plaCode'])) ? $_SESSION['plaCode'] : "Sem Player 1";
        $plaCode2 = (isset($_SESSION['plaCode2'])) ? $_SESSION['plaCode2'] : "Sem Player 2";

        $roomList = (isset($_SESSION['roomList'])) ? $_SESSION['roomList'] : null;
        $roomListCount = (isset($_SESSION['roomList'])) ? count($roomList) : 0;
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
                <div class="search-bar">
                    <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
                </div>
            </div> 
        </nav>
    </div>

    <br><br><br><br>

    <div>
        <h1>Sala de Batalha</h1>
        <form method="POST" action="../Controller/addRoom.php">
            <p>Criar Sala: <input type="submit" name="addRoom"/></p>
        </form>
        <form method="POST" action="../Controller/editRoom.php">
            Entrar na Sala (por ID): <input type="text" name="roomCode" placeholder="Código da Sala" required></input>
            <input type="submit" name="editRoom"/>
        </form>
    </div>

    <div>
        <h1>Salas</h1>
        <ul id="room-list"></ul>
        <form method="POST" action="../Controller/roomListServer.php">
            <p>Atualizar Lista: <input type="submit" name="roomList"/></p>
        </form>
    </div>

    <div>
        <form method="POST" action="../Controller/deleteRoom.php">
            <p>Sair/Deletar Sala: <input type="submit" name="roomList"/></p>
        </form>
    </div>

    <!-- Atualiza a Lista a cada 500ms -->
    <script src="../Model/js/room/roomListUpdate.js"></script>
</body>
</html>