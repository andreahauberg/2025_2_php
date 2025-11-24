<?php

try{
    session_start();
    require_once __DIR__."/../x.php";
    $userEmail = _validateEmail();
    $userPassword = _validatePassword();
    require_once __DIR__."/../db.php";
    $sql = "SELECT * FROM users WHERE user_email = :email";
    $stmt = $_db->prepare( $sql );

    $stmt->bindValue(":email", $userEmail);
    $stmt->execute();
    $user = $stmt->fetch();
    // echo $user;
    // var_dump($user);
    // echo "<br>";
    // print_r($user); 
    // echo "<br>";
    // echo json_encode($user);
    if(!$user || !password_verify($userPassword, $user["user_password"])){
        // hvis brugeren ikke eksisterer eller adgangskoden/email er forkert 
        $_SESSION['toast'] = [ 'message' => 'Wrong email or password', 'type' => 'error' ];
        // hold dialog boksen åben 
        $_SESSION['open_dialog'] = 'login'; 
        header("Location: /");
        exit();
    }

    unset($user["user_password"]);
    $_SESSION["user"] = $user;
    header("Location: /home");

}catch(Exception $e){
    $_SESSION['toast'] = ['message' => $e->getMessage(), 'type' => 'error'];
    $_SESSION['open_dialog'] = 'login';
    header('Location: /');
    exit();
}

