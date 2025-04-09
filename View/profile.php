<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/styleProfile.css">
    <title>Perfil</title>
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION['user']) == true) {
            header('Location: login.php');
            exit();
        }
        if (isset($_SESSION['success']) == true) {
            echo "<script>alert('" . $_SESSION['success'] . "');</script>";
            unset($_SESSION['success']); // Limpa a mensagem depois de mostrar
        }
        if (isset($_SESSION['error']) == true) {
            echo "<script>alert('" . $_SESSION['error'] . "');</script>";
            unset($_SESSION['error']); // Limpa a mensagem depois de mostrar
        }
        if (isset($_SESSION['message']) == true) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
            unset($_SESSION['message']); // Limpa a mensagem depois de mostrar
        }

        $name = $_SESSION['user']['plaName'];
        $email = $_SESSION['user']['plaEmail'];

        //define a foto do usuario
        if($_SESSION['user']['plaPhoto'] != null){
            $profilePicture = $_SESSION['user']['plaPhoto']; // Substitua pelo caminho da imagem de perfil do usuário
            $fileType = $_SESSION['user']['plaPhotoType']; // Tipo de arquivo da imagem
        } else {
            $profilePicture = "./Images/user-profile-default.png"; // Substitua pelo caminho padrão da imagem de perfil
            $fileType = "image/png"; // Tipo de arquivo padrão
        }
    ?>

    <h1>Perfil</h1>

    <!-- Formulário de upload de imagem de perfil -->
    <div class="container">
        <!-- Imagem de perfil -->
        <form enctype="multipart/form-data" action="../Controller/uploadPhoto.php" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="999999999"/>

            <div class="profile-image">
                <a onmouseover="" class="img-button"><img data-tooltip="Editar Foto de Perfil" src="data:<?php echo $fileType ?>;base64,<?php echo $profilePicture; ?>" alt="Imagem" class="profile-image"></a> <!-- Perfil Usuario -->
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
            <button type="button" onclick="showPassword()">see senha</button>
            <br>
            <label id="confirmar-senha" style="display: none" for="confirmar-senha">Confirmar Senha:</label>
            <input id="confirmar-senha-input" style="display: none" type="password" placeholder="Confirme sua nova senha." name="confirmarSenha">
            <br>
            <button type="submit" name="updateButton">Salvar Alterações</button>
        </form>
    </div>
</body>

<script>
    function showPassword() {
        let senha = document.getElementById("senha").type;
        if(senha == "text"){
            document.getElementById("senha").type = "password";
        } else {
            document.getElementById("senha").type = "text";
        }
    };

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

    document.querySelector('.profile-image').addEventListener('click', function() {
        document.querySelector('.profile-popup').classList.toggle('hidden');
    });
</script>

</html>