<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styleProfile.css">
    <link rel="stylesheet" href="Style/styles.css">
    <title>Perfil</title>
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION['user']) == true) {
            header('Location: login.php');
            exit();
        }
        if (isset($_SESSION['message']) == true) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
            unset($_SESSION['message']); // Limpa a mensagem depois de mostrar
        }

        $name = $_SESSION['user']['plaName'];
        $email = $_SESSION['user']['plaEmail'];

        $photo = $_SESSION['user']['plaPhoto'];

        //define a foto do usuario
        if($photo != null){
            $profilePicture = $photo; // Substitua pelo caminho da imagem de perfil do usuário
            $fileType = $_SESSION['user']['plaPhotoType']; // Tipo de arquivo da imagem
        } else {
            $profilePicture = "./Images/user-profile-default.png"; // Substitua pelo caminho padrão da imagem de perfil
            $fileType = "image/jpeg"; // Tipo de arquivo padrão
        }

        echo "<img src='data=image/jpeg;base64;./Images/user-profile-default.png'>";
    ?>

    <h1>Perfil</h1>

    <!-- Formulário de upload de imagem de perfil -->
    <div id="app" class="container">
        <nav class="navbar">
            <div class="container">
                <a href="/" class="logo">PokéParty</a>
                <div class="nav-links">
                    <a href="index.php">Home</a> 
                    <a href="pokedex.php">Pokédex</a>
                    <a href="teams.php">Times</a>
                    <a href="roomList.php">Salas</a>
                    <!-- <a href="battle.php">Batalha</a> -->
                    <a href="profile.php" class="active">Perfil</a>
                </div>
                <div class="search-bar">
                    <input type="search" id="pokemon-search" placeholder="Buscar Pokémon...">
                </div>
            </div>
        </nav>
        <br><br>
        <!-- Imagem de perfil -->
        <form enctype="multipart/form-data" action="../Controller/uploadPhoto.php" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="999999999"/>

            <div class="profile-image">
                <a onmouseover="" class="img-button"><img name="imagemCorreta" data-tooltip="Editar Foto de Perfil" src="data:<?php echo $fileType ?>;base64,<?php echo $profilePicture; ?>" alt="Imagem" class="profile-image"></a> <!-- Perfil Usuario -->
                <div class="profile-popup hidden">sadzxc</div> <!-- div oculta para o botão de upload -->
            </div>

            <div><input name="imagem" type="file" src="data:<?php echo $fileType ?>;base64,<?php echo $profilePicture; ?>" alt="Imagem"/></div>
            <div><input type="submit" value="Salvar"/></div>
        </form>
            
        <!-- Formulário de edição de perfil -->
        <form action="../Controller/updateProfileServer.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="<?php echo $name ?>">
            <br>
            <label for="email">Email: (Não é possível trocar o email)</label>
            <input readonly="true" type="email" id="email" value="<?php echo $email ?>" name="email">
            <br>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" oninput="inputDetect()" placeholder="Digite AQUI caso queira alterar a sua senha." name="senha">
            <button type="button" onclick="showPassword()">Ver Senha</button>
            <br>
            <label id="confirmar-senha" style="display: none" for="confirmar-senha">Confirmar Senha:</label>
            <input id="confirmar-senha-input" style="display: none" type="password" placeholder="Confirme sua nova senha." name="confirmarSenha">
            <br>
            <button type="submit" name="updateButton">Salvar Alterações</button>
        </form>
    </div>
</body>

<script>
    //mostra e esconde a senha
    function showPassword() {
        let senha = document.getElementById("senha").type;
        if(senha == "text"){
            document.getElementById("senha").type = "password";
        } else {
            document.getElementById("senha").type = "text";
        }
    };

    //botao para mostrar e esconder a senha de confirmacao (so vai mostrar se ela comecar a digitar a senha)
    function inputDetect() {
        let text = document.getElementById("senha").value;
        if(text == ""){
            document.getElementById("confirmar-senha").style.display = "none";
            document.getElementById("confirmar-senha-input").style.display = "none";
        } else {
            document.getElementById("confirmar-senha").style.display = "block";
            document.getElementById("confirmar-senha-input").style.display = "block";
        }
    }

    //faz surgir o popup que vai ser utilizado para o envio da imagem
    document.querySelector('.profile-image').addEventListener('click', function() {
        document.querySelector('.profile-popup').classList.toggle('hidden');
    });

    //conserta a imagem Default de perfil
    document.addEventListener('DOMContentLoaded', function() {
        const profileImageInput = document.querySelector('img[name="imagemCorreta"]');
        const profileImageSrc = profileImageInput.getAttribute('src');

        if (profileImageSrc.includes('base64') && profileImageSrc.includes('./Images/user-profile-default.png')) {
            const updatedSrc = '<?php echo $profilePicture ?>';
            profileImageInput.setAttribute('src', updatedSrc);
        }
    });

</script>

</html>