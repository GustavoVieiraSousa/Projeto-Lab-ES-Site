<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="Style/styleBattle.css">
        <title>Batalhas</title>
    </head>

    <body>
        <?php
            session_start();
            //require_once '../Controller/waitBattle.php';
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

            // VERIFICA SE O PLAYER É VITORIOSO OU NAO
            if(isset($_SESSION['battle']['win'])){
                // Ação da tela de vitoria ou derrota;
            }
        ?>

        <main class="container">
            <screen class="wait-popup">Waiting for the second player...</screen>
            <div class="screen">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/1.png" alt="Pokémon Player 1">
            </div>
            <div class="screen">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/4.png" alt="Pokémon Player 2">
            </div>
            <div class="screen">
                <?php showPokemon(); ?>
            </div>

            

        </main>

        <form method="POST" action="../Controller/resignBattle.php">
            <button type="resign" name="resign">Desistir</button> <?php var_dump($_SESSION['battle']) ?>
        </form>
    </body>
</html>

<!-- Atualiza a Pagina para pesquisar se o segundo player está Pronto ou nao | Libera a pagina ou nao -->
<?php 
    //Tratando um errinho chato que sempre aparece o Wait toda vez q a pagina é carregada
    echo "<script src='../Model/js/battle/isReady.js'></script>";
    if($_SESSION['battle']['ready'] == true){
        echo "<script> document.querySelector('.wait-popup').classList.add('hidden'); </script>";
        $_SESSION['battle']['ready'] == false;
    }
?>

<?php
    function showPokemon(){
        echo "<h1>Pokémon Battle</h1>";
        for ($i = 0; $i < 5; $i++) {
            echo "<img src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/$i.png' alt='Pokémon $i' class='pokemon-image'>";
        }
    }
?>
