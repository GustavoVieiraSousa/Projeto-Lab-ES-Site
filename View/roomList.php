<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styleRoom.css">
    <title>Salas</title>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchRoomList();
        });
</script>
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

        if (isset($_SESSION['roomCode'])){
            header('Location: lobby.php');
            exit();
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
                <div class="place-holder">
                    <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
                </div>
            </div> 
        </nav>
    </div>

    <!-- Search -->
    <content>
        <div class="content-search">
            <h1>Sala de Batalha</h1>
            
            <form method="POST" class="search-id" action="../Controller/editRoom.php">
                <p>Entrar na Sala:</p>
                <input type="text" class="search-bar" name="roomCode" placeholder="ID da Sala" required></input>
                <input type="submit" class="search-bar-button" name="editRoom" value="Entrar na Sala"/>
            </form>

            <updatelist>
                <form method="POST" action="../Controller/addRoom.php">
                    <input type="submit" onclick="togglePopup()" name="addRoom" value="Criar Sala"/>
                </form>
                <form method="POST" action="../Controller/roomListServer.php">
                    <input type="submit" name="roomList" value="Atualizar"/>
                </form>
                <form method="POST" action="../Controller/deleteRoom.php">
                    <input type="submit" name="roomList" value="Sair da Sala"/>
                </form>
            </updatelist>
        </div>

        <!-- Lista -->
        <div class="content-list">
            <h1>Salas</h1>
            <table class="room-list-header">
                <tr class="room-list">
                    <th class="room-id">Room ID</th>
                    <th class="room-player1">Player 1 ID</th>
                    <th class="room-player2">Player 2 ID</th>
                    <th class="room-action">Entrar na Sala</th>
                </tr>
            </table>

            <!-- Mostra as salas do Banco de Dados -->
            <table id="room-list" class="room-list-content"></table> 
        </div>
    </content>

    <!-- Atualiza a Lista a cada 500ms -->
    <script src="../Model/js/room/roomListUpdate.js"></script>
</body>
</html>