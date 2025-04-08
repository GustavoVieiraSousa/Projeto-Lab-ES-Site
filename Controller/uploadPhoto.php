<?php
require("connection.php");
session_start();
$imagem = $_FILES['imagem']['tmp_name'];
$tamanho = $_FILES['imagem']['size'];
$email = $_SESSION['user']['plaEmail'];

echo "VAR DUMP: ",var_dump($tamanho);
if ($tamanho > 26214400) { // 25MB
    $_SESSION['error'] = 'Imagem muito grande (Máx: 25MB)';
    header('Location: ../View/profile.php');
    exit();
}

//esqueci que tava comentando em inglês, vou deixar assim mesmo (não vou traduzir tudo agora). Nota: O copilot traduziu pra mim, cara bom.
if ( $imagem != null ){
    //open the file in binary mode and read it
    $fp = fopen($imagem, "rb");
    $conteudo = fread($fp, $tamanho); //reads the image file and stores it in the variable 'conteudo' as binary data (The "rb" is from fopen)
    fclose($fp);

    //updates the user's profile picture in the database
    $stmt = $conn->prepare("UPDATE player SET plaPhotoBlob = ? WHERE plaEmail = ?");
    $stmt->execute([$conteudo, $email]);

    //fetch the image from the database again to display it on the screen
    $getPhotoBlob = $conn->prepare("SELECT plaPhotoBlob FROM player WHERE plaEmail = ?");
    $getPhotoBlob->execute([$email]);
    $photoBlob = $getPhotoBlob->fetch(PDO::FETCH_ASSOC);

    //check if the image exists in the database and if the upload was successful
    if($photoBlob != null){
        $base64 = base64_encode($photoBlob['plaPhotoBlob']); //convert the binary data to base64
        $_SESSION['user']['plaPhoto'] = $base64; //base64 saved on the session
        //var_dump($_SESSION['user']['plaPhoto']);
        //echo "<img src='data:image/jpeg;base64,".$_SESSION['user']['plaPhoto']."' alt='Imagem' class='profile-image'>";
        //$getPhotoBlob = $conn->query("SELECT plaPhotoBlob FROM player WHERE plaEmail = ?");
        $_SESSION['success'] = 'Imagem carregada com sucesso!';
        header('Location: ../View/profile.php');
    } else {
        $_SESSION['error'] = "Erro ao atualizar a imagem de perfil.";
        header('Location: ../View/profile.php');
    }
    exit();
}
else{
    $_SESSION['error'] = 'Nenhuma imagem foi inserida! (Server Timeout)';
    header('Location: ../View/profile.php');
}    
?>