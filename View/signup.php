<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="Style/style.css">
    <title>pkLogin</title>
</head>

<body>

<?php
    session_start(); // Inicia a sessão para usar mensagens de erro
    if (isset($_SESSION['error'])) {
        echo "<script>alert('" . $_SESSION['error'] . "');</script>";
        unset($_SESSION['error']); // Limpa a mensagem depois de mostrar
    }
?>

    <!-- Título com a imagem -->
    <div class="title">
        <img src="./Images/pokeParty-Logo.png" alt="Poke Party">
    </div>

    <!-- Formulário de login -->
    <main class="container">
        <form method="POST" name="paia" action="../Controller/signupServer.php" class="login-form">
            <h1>Cadastro</h1>

            <div class="input-box">
                <input placeholder="Nome" type="text" name="name" required>
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input placeholder="Email" type="email" name="email" required>
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="password" required>
                <i class="bx bxs-lock-alt"></i>
            </div>

            <button type="submit" class="login" name="ze" value="signup">Cadastrar</button>
            <div class="register-link">
                <p>Não tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </form>
    </main>

    <!-- Imagens decorativas -->
    <img src="./img/pikachu.png" alt="Pikachu" class="bg-img pikachu">
    <img src="./img/pokebola.png" alt="Pokebola" class="bg-img pokebola">

</body>

</html>
