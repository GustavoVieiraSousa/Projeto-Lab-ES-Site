<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Home PokeParty</title>
        <link rel="stylesheet" href="./Style/styleHome.css">
        <style>
            .corpo {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-top: 40px;
            }
            .corpo h2 {
                color: #2a75bb;
                text-align: center;
                margin-bottom: 18px;
            }
            .corpo p {
                color: #2a75bb;
                font-size: 1.15rem;
                max-width: 600px;
                text-align: center;
                margin-top: 18px;
            }
        </style>
    </head>

    <body>
      <nav class="navbar">
        <div class="container">
          <a href="/" class="logo">PokéParty</a>
          <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="pokedex.php">Pokédex</a> 
            <a href="teams.php">Times</a>
            <a href="roomList.php">Salas</a>
            <!-- <a href="battle.php">Batalha</a> -->
            <a href="profile.php">Perfil</a>
          </div>
          <div class="place-holder">
            <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
          </div>
        </div>
      </nav>

        <main class="corpo">
          <br>
          <br>
            <h2>Bem-vindo ao PokéParty!</h2>
            <p>
                PokéParty é uma plataforma online onde você pode montar seus próprios times de Pokémon, desafiar amigos em batalhas por turnos, explorar a Pokédex e participar de salas de batalha.<br>
                Crie estratégias, escolha seus Pokémon favoritos e mostre que você é um verdadeiro mestre Pokémon!
            </p>
        </main>
    </body>
</html>