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
            require_once '../Controller/TeamController.php';
            require_once '../Controller/getPlayers.php';

            if (!isset($_SESSION['user']) || !$_SESSION['user']) {
                header('Location: login.php');
                exit();
            }

            if (!isset($_SESSION['roomCode'])){
                header('Location: roomList.php');
                exit();
            }

            if (!isset($_SESSION['battle']['teamId'])){
              header('Location: lobby.php');
              exit();
            }
            
            if (isset($_SESSION['message'])) {
                echo "<script>alert('" . $_SESSION['message'] . "');</script>";
                unset($_SESSION['message']); // Limpa a mensagem depois de mostrar
            }

            // VERIFICA SE O PLAYER É VITORIOSO OU NAO
            if(isset($_SESSION['end']) == 1){
                unset($_SESSION['end']);
                $_SESSION['message'] = 'você venceu :D';
                header("Location: lobby.php");
                exit();
            }

            $pokemon1Sprite = "./Images/pokemon-default.png";
            $pokemon2Sprite = "./Images/pokemon-default.png";

            $player1 = $_SESSION['battle']['player1'];
            $plaCode = $_SESSION['plaCode'];
            $activePlayer = ($plaCode == $player1) ? "pokemon1" : "pokemon2";

        ?>

         <div id="app">
       <nav class="navbar">
            <div class="container">
                <a href="/" class="logo">PokéParty</a>
                <div class="nav-links">
                    <a href="index.php">Home</a> 
                    <a href="pokedex.php">Pokédex</a>
                    <a href="teams.php">Times</a>
                    <a href="roomList.php">Salas</a>
                   
                    <a href="profile.php" class="active">Batalha</a>
                </div>
                 <div class="place-holder">
            <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
          </div>
            </div>
        </nav>
        <br><br>

      <main class="container">
        <h2 class="page-title">Batalha Pokémon</h2>

        <div class="battle-scene">
          <div class="pokedex-container">
            <div class="pokedex enemy">
              <div class="pokedex-top">
                <div class="pokedex-lights">
                  <div class="main-light"></div>
                  <div class="small-lights">
                    <div class="small-light"></div>
                    <div class="small-light"></div>
                    <div class="small-light"></div>
                  </div>
                </div>
              </div>
              
              <div class="pokedex-screen-container">
                <div class="pokedex-screen">
                  <div class="pokemon-sprite">
                    <img class="img-pokemon-sprite1" src="<?php echo $pokemon1Sprite ?>" alt="Pokemon 1">
                  </div>
                  <div class="pokemon-info">
                    <div class="pokemon-name pokName1">Pokemon 1</div>
                    <div class="pokemon-level">Nv. 100</div>
                    <div class="hp-container">
                      <div class="hp-label">HP:</div>
                      <div class="hp-bar">
                        <div class="hp-fill percHpP1"></div>
                      </div>
                      <div class="hp-values hpP1">??/??</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Card Blue - Player 1 -->
              <div class="pokedex-bottom">
                <div class="team-pokeballs">
                  <div class="pokeballs-container">
                    <div class="pokeball active"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                  </div>
                </div>

                <!-- Ataques -->
                <div class="moves-container">
                  <button class="move-btn atk1 electric" data-attack="1">Ataque 1</button> <!-- TODO: Travar o botao quando for clicado e nao destravar até atualizar o round -->
                  <button class="move-btn atk2 normal" data-attack="2">Ataque 2</button>
                  <button class="move-btn atk3 electric" data-attack="3">Ataque 3</button>
                  <button class="move-btn atk4 normal" data-attack="4">Ataque 4</button>
                </div>

                <div class="surrender-container">
                    <form method="POST" action="../Controller/resignBattle.php">
                    <button type="resign" name="resign" class="surrender-btn">Desistir</button> 
                    </form>
                </div>
              </div>
            </div>
           
            <!-- Center battle status -->
            <div class="battle-status-container">
              <div class="battle-status">VS</div>
            </div>
            
            <!-- Player Pokedex (Red) -->
            <div class="pokedex player">
              <div class="pokedex-top">
                <div class="pokedex-lights">
                  <div class="main-light"></div>
                  <div class="small-lights">
                    <div class="small-light"></div>
                    <div class="small-light"></div>
                    <div class="small-light"></div>
                  </div>
                </div>
              </div>
              
              <div class="pokedex-screen-container">
                <div class="pokedex-screen">
                  <div class="pokemon-sprite">
                    <img class="img-pokemon-sprite2" src="<?php echo $pokemon2Sprite ?>" alt="Pokemon 2">
                  </div>
                  <div class="pokemon-info">
                    <div class="pokemon-name pokName2">Pokemon 2</div>
                    <div class="pokemon-level">Nv. 100</div>
                    <div class="hp-container">
                      <div class="hp-label">HP:</div>
                      <div class="hp-bar">
                        <div class="hp-fill percHpP2"></div>
                      </div>
                      <div class="hp-values hpP2">??/??</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="pokedex-bottom">
                <div class="team-pokeballs">
                  <div class="pokeballs-container">
                    <div class="pokeball active"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                    <div class="pokeball"></div>
                  </div>
                </div>               
              </div>
            </div>
          </div>
          
          <!-- battle log -->
          <div class="battle-log">
            <div class="log-header">BATTLE LOG</div>
            <div class="log-content">
              <!-- <p>A wild Charizard appeared!</p>
              <p>Pikachu used Thunderbolt!</p>
              <p>It's not very effective...</p> -->
            </div>
          </div>

          <div class="battle-controls">
            <form method="POST" action="../Controller/resignBattle.php">
                <button type="resign" name="resign" class="battle-option-btn">Desistir</button>
            </form>

          </div>
        </div>
      </main>
    </div>
    <screen class="wait-popup">Waiting for the second player...</screen> <!-- Tela que barra as ações do player -->
    </body>
</html>

<script>
const atkNames = {
    1: "<?php echo isset($_SESSION['battle'][$activePlayer]['atkName1']) ? $_SESSION['battle'][$activePlayer]['atkName1'] : 'Ataque 1'; ?>",
    2: "<?php echo isset($_SESSION['battle'][$activePlayer]['atkName2']) ? $_SESSION['battle'][$activePlayer]['atkName2'] : 'Ataque 2'; ?>",
    3: "<?php echo isset($_SESSION['battle'][$activePlayer]['atkName3']) ? $_SESSION['battle'][$activePlayer]['atkName3'] : 'Ataque 3'; ?>",
    4: "<?php echo isset($_SESSION['battle'][$activePlayer]['atkName4']) ? $_SESSION['battle'][$activePlayer]['atkName4'] : 'Ataque 4'; ?>"
};
</script>

<script>
document.querySelectorAll('.move-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // Desabilita todos os botões de ataque
        document.querySelectorAll('.move-btn').forEach(btn => { btn.setAttribute('data-original', btn.textContent); });
        document.querySelectorAll('.move-btn').forEach(b => { b.disabled = true; b.textContent = "Aguarde..."; } );

        const attack = this.getAttribute('data-attack');
        fetch('../Controller/attackBattle.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ attack })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const atkName = atkNames[data.attack] || "Ataque";
                const logContent = document.querySelector('.log-content');
                const crit = data.crit == 2 ? "Ataque CRÍTICO." : "";
                const p = document.createElement('p');
                if(data.damage == 0){ p.textContent = `Você usou ${atkName}! O ataque não causou dano.` }
                else{ p.textContent = `Você usou ${atkName}! (Dano: ${data.damage}). ${crit}`; }
                logContent.appendChild(p);
                logContent.scrollTop = logContent.scrollHeight;
            } else {
                alert('Erro ao atacar: ' + (data.error || ''));
            }
        });
    });
});
</script>

<!-- Atualiza a Pagina para pesquisar se o segundo player está Pronto ou nao | Libera a pagina ou nao -->
<?php 
    //Tratando um errinho chato que sempre aparece o Wait toda vez q a pagina é carregada
    echo "<script src='../Model/js/battle/isReady.js'></script>";

    // Adiciona o código JavaScript para esperar o evento
    echo "
        <script>
            document.addEventListener('isReadyComplete', function() {
                // Executa os scripts subsequentes após isReady.js terminar
                document.querySelector('.wait-popup').classList.add('hidden');
                const script = document.createElement('script');
                script.src = '../Model/js/battle/updateBattle.js';
                document.body.appendChild(script);
            });
        </script>
    ";
?>

