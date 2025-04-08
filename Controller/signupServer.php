<?php
//database connection & session start
require_once('connection.php');
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        echo "Necessário preencher todos os campos.";
        exit();
    }
    
    // Hash the password
    $algorithm = PASSWORD_BCRYPT;
    $options = ['cost' => 12];
    $hashedPassword = password_hash($password, $algorithm, $options);

    $duplicatedEmail = $conn->prepare("SELECT plaEmail FROM player WHERE plaEmail = ?");
    $duplicatedEmail->execute([$email]);
    $duplicatedEmail->setFetchMode(PDO::FETCH_ASSOC);

    if($email == $duplicatedEmail->fetchColumn()){
        $_SESSION['error'] = "Email já está registrado.";
        header("Location: ../View/signup.php");
        exit();
    }

    // Insert user into the database
    $sql = "INSERT INTO player (plaName, plaEmail, plaPassword) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        $_SESSION['success'] = "Cadastro realizado com sucesso.";
        header("Location: ../View/login.php");
        exit();

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { 
            echo "Email já está registrado.";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>