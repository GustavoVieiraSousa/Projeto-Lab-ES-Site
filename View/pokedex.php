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
    if (isset($_SESSION['success'])) {
      echo "<script>alert('" . $_SESSION['success'] . "');</script>";
      unset($_SESSION['success']); // Limpa a mensagem depois de mostrar
    }
  ?>

    <div id="app">
      <nav class="navbar">
        <div class="container">
          <a href="/" class="logo">PokéParty</a>
          <div class="nav-links">
            <a href="index.php">Home</a> 
            <a href="pokedex.php" class="active">Pokédex</a>
            <a href="teams.php">Times</a>
            <a href="roomList.php">Salas</a>
            <!-- <a href="battle.php">Batalha</a> -->
            <a href="profile.php">Perfil</a>
          </div>
          <div class="search-bar">
            <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
          </div>
        </div>
      </nav>

      <br><br><br><br>    

      <main class="container">
        <div class="pokemon-grid" id="pokemon-list2">
          <!-- Pokémon cards serão inseridos aqui via JavaScript -->
          <div class="loading">Carregando Pokémon...</div>
        </div>
        <div class="pagination" id="pagination">
          <!-- Paginação será inserida aqui -->
        </div>
      </main>
    </div>

    <script src="../Model/js/api.js"></script>
    <script src="../Model/js/pokedex.js"></script>
  </body>
</html>