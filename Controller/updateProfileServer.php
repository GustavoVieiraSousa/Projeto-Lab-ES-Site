<?php
    require_once('connection.php');
    session_start();

    $email = $_SESSION['user']['plaEmail'];
    $name = $_SESSION['user']['plaName'];
    $password = $_SESSION['user']['plaPassword'];

    $updateName = $_POST['nome'];
    $updatePassword = $_POST['senha'];
    $updatePasswordConfirm = $_POST['confirmarSenha'];

    $nameSuccess = null;
    $passwordSuccess = null;

    $isUpdatingName = false;
    $isUpdatingPassword = false;

    //verifica se o usuário está logado
    if(!isset($_SESSION['user'])){
        header('Location: ../View/login.php');
        exit();
    }

    //verifica se o usuário clicou no botão de atualizar
    if(!isset($_POST['updateButton'])){
        $_SESSION['message'] = "Erro ao atualizar o perfil.";
        header('Location: ../View/profile.php');
        exit();
    }

    //verifica se o usuário digitou algo no Nome
    if(!empty($updateName)){
        $isUpdatingName = true;
        if($updateName == $name){ //compara se o nome é diferente do que está no banco de dados
            $_SESSION['message'] = "Nome já está registrado. Nenhuma alteração foi feita.";
            header('Location: ../View/profile.php');
            exit();
        }
        else{
            $stmtName = $conn->prepare("UPDATE player SET plaName = ? WHERE plaEmail = ?");
        }
    }

    //verifica se o usuário digitou algo na Senha
    if(!empty($updatePassword) && !empty($updatePasswordConfirm)){ //usuario digitou a senha e a confimacao
        $isUpdatingPassword = true;
        if($updatePassword != $updatePasswordConfirm){ //compara se as senhas são diferentes
            $_SESSION['message'] = "As senhas não coincidem. Nenhuma alteração foi feita.";
            header('Location: ../View/profile.php');
            exit();
        }
        else{
            // Hash the password
            $algorithm = PASSWORD_BCRYPT;
            $options = ['cost' => 12];
            $hashedPassword = password_hash($updatePassword, $algorithm, $options);

            //atualiza a senha no banco de dados
            $stmtPassword = $conn->prepare("UPDATE player SET plaPassword = ? WHERE plaEmail = ?");
        }
    }

    if($isUpdatingName && !$isUpdatingPassword){ //atualiza apenas o nome
        $stmtName->execute([$updateName, $email]);
        $_SESSION['user']['plaName'] = $updateName; //atualiza o nome na sessão
        $nameSuccess = "Nome atualizado com sucesso."; //mensagem

        $_SESSION['message'] = "$nameSuccess"; //mensagem de sucesso
    }
    else if(!$isUpdatingName && $isUpdatingPassword){ //atualiza apenas a senha
        $stmtPassword->execute([$hashedPassword, $email]);
        $passwordSuccess = "Senha atualizada com sucesso."; //mensagem

        $_SESSION['message'] = "$passwordSuccess"; //mensagem de sucesso
    }
    else if($isUpdatingName && $isUpdatingPassword){ //nenhuma atualização foi feita
        $stmtName->execute([$updateName, $email]);
        $_SESSION['user']['plaName'] = $updateName; //atualiza o nome na sessão
        $nameSuccess = "Nome atualizado com sucesso."; //mensagem

        $stmtPassword->execute([$hashedPassword, $email]);
        $passwordSuccess = "Senha atualizada com sucesso."; //mensagem

        $_SESSION['message'] = "$nameSuccess $passwordSuccess"; //mensagem de sucesso
    }
    else{
        $_SESSION['message'] = "Nenhuma alteração foi feita.";
    }

    header('Location: ../View/profile.php');
    exit();
?>