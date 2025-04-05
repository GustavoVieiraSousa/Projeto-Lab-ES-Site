<?php
    require_once('connection.php');
    session_start();
    session_regenerate_id(true);

    //login using pdo
    if (!isset($_POST['login'])) {
        echo "<script>alert('Por favor, realize o Login antes.');</script>";
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
    
    //clean the password from the database
    $passwordGET = trim(implode(",", $passwordGETv));

    //verify stored hash against plain-text password
    if (password_verify($password, $passwordGET)) {
        $stmt = $conn->prepare("SELECT * FROM player WHERE plaEmail = ? AND plaPassword = ?");
        $stmt->execute([$email, $passwordGET]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //if the user is not found, redirect to login page with an error message
    if (!$user) {
        header("Location: ../View/login.php");
        echo "<script>alert('Senha ou email incorretos.');</script>";
        exit();
    }
    $_SESSION['user'] = $user;
    $_SESSION['plaCode'] = $user['plaCode'];
    header("Location: ../View/pokedex.php");
    exit();
?>