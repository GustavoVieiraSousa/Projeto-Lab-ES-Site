<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PokéParty</title>
    <link rel="stylesheet" href="Style/styles.css">
    <meta name="description" content="PokéParty - Pokedex e Times Pokemon" />
  </head>

  <body>
  <?php
    session_start();
    if (!isset($_SESSION['user']) == true) {
        header('Location: login.php');
        exit();
    }
  ?>

    <div id="app">
      <nav class="navbar">
        <div class="container">
          <a href="/" class="logo">PokéParty</a>
          <div class="nav-links">
            <a href="/">Home</a> 
            <a href="pokedex.php" class="active">Pokédex</a>
            <a href="teams.php">Times</a>
            <a href="battle.php">Batalha</a>
          </div>
          <div class="search-bar">
            <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
          </div>
        </div>
      </nav>

      <main class="container">
        <h2 class="page-title">Pokédex</h2>
        <div class="pokemon-grid" id="pokemon-list">
          <!-- Pokémon cards serão inseridos aqui via JavaScript -->
          <div class="loading">Carregando Pokémon...</div>
        </div>
        <div class="pagination" id="pagination">
          <!-- Paginação será inserida aqui -->
        </div>
      </main>
    </div>

    <!-- Destroys the session created -->
    <form method="POST" action="../Controller/loginServer.php" class="login-form">
      <button  type="sair" value="sair" name="sair">
        Sair da Conta 
        <?php 
          if(isset($_POST['sair'])){
            session_destroy(); 
            header ("location: login.php");
          }
        ?>
      </button>
    </form>
    
    <script src="../Model/js/api.js"></script>
    <script src="../Model/js/pokedex.js"></script>
  </body>
</html>