<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styleBattle.css">
    <title>Batalhas</title>
</head>
<!-- filepath: c:\xampp\htdocs\Projeto-Lab-ES-Site\View\battle.php -->
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
?>

<div class="container">
    <div class="screen">
        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/1.png" alt="Pokémon Player 1">
    </div>
    <div class="screen">
        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/4.png" alt="Pokémon Player 2">
    </div>
</div>
        
</body>
</html>

<!--
<?php
    for ($i = 0; $i < 5; $i++) {
        echo "<h1>Pokémon Battle</h1>
        <img src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/$i.png' alt='Pokémon $i' class='pokemon-image'>
        <h2>Nome do Pokémon $i</h2>
        <p>HP: 100</p>
        <p>Tipo: Fogo</p>
        <p>Movimento: Flamethrower</p>";
    }
?>


-->