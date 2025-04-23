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

        //define a foto do usuario
        if($_SESSION['user']['plaPhoto'] != null){
            $profilePicture = $_SESSION['user']['plaPhoto']; // Substitua pelo caminho da imagem de perfil do usuário
        } else {
            $profilePicture = "./Images/user-profile-default.png"; // Substitua pelo caminho padrão da imagem de perfil
        }
    ?>

    <h1>Perfil</h1>

    <!-- Formulário de upload de imagem de perfil -->
    <div class="container">
        <!-- Imagem de perfil -->
        <form id="profilePicture" enctype="multipart/form-data" action="../Controller/uploadPhoto.php" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="999999999"/>

            <div class="profile-image">
                <a href="#" onmouseover="" class="img-button"><img data-tooltip="Editar Foto de Perfil" src="data:image/jpeg;base64,<?php echo $profilePicture; ?>" alt="Imagem" class="profile-image"> <!-- Perfil Usuario -->
                <div class="profile-popup hidden">sadzxc</div> <!-- div oculta para o botão de upload -->
            </div>

            <div><input name="imagem" type="file" src="data:image/jpeg;base64,<?php echo $profilePicture; ?>" alt="Imagem"/></div>
            <div><input type="submit" value="Salvar"/></div>

            <script>
                document.querySelector('.img-button').addEventListener('click', function() {
                    document.querySelector('.profile-popup').classList.toggle('hidden');
                });
            </script>

        </form>
            

        <!-- Formulário de edição de perfil -->
        <form action="#" method="post">
            
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <br>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>

</html>