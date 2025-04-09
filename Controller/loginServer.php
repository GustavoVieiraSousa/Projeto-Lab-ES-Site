<?php
    //database connection & session start
    require_once('connection.php');
    session_start();
    session_regenerate_id(true);

    //login using pdo
    if (!isset($_POST['login'])) {
        $_SESSION['error'] = 'Por Favor, faça login.';
        header("Location: ../View/login.php");
        exit();
    }

    //set the password hash algorithm and options
    $algorithm = PASSWORD_BCRYPT;
    $options = ['cost' => 12];

    //recieve the data from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    //get the password from the database
    $passStmt = $conn->prepare("SELECT plaPassword FROM player WHERE plaEmail = ?");
    $passStmt->execute([$email]);
    $passwordGETv = $passStmt->fetch(PDO::FETCH_ASSOC);
    
    //clean the password variable
    $passwordGET = trim(implode(",", $passwordGETv));

    //verify stored hash against plain-text password
    if (password_verify($password, $passwordGET)) {
        $stmt = $conn->prepare("SELECT * FROM player WHERE plaEmail = ? AND plaPassword = ?");
        $stmt->execute([$email, $passwordGET]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //if the user is not found, redirect to login page with an error message
    if (!$user) {
        $_SESSION['error'] = 'Email ou senha incorretos.';
        header("Location: ../View/login.php");
        exit();
    }
    $_SESSION['user'] = $user;
    $_SESSION['user']['plaPhoto'] = base64_encode($user['plaPhotoBlob']);//store the profile picture path in the session
    $_SESSION['user']['plaPhotoType'] = $user['plaPhotoBlobType']; 
    $_SESSION['user']['plaEmail'] = $user['plaEmail']; //store the email in the session (for security reasons, this should be avoided)
    $_SESSION['user']['plaName'] = $user['plaName']; //store the name in the session (for security reasons, this should be avoided)
    $_SESSION['user']['plaPassword'] = $user['plaPassword']; //store the password in the session (for security reasons, this should be avoided)
    
    
    $_SESSION['success'] = 'Login realizado com sucesso!';
    header("Location: ../View/pokedex.php");
    exit();
?>