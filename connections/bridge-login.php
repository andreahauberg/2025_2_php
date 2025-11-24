<?php

try{
    require_once __DIR__."/../x.php";
    $userEmail = _validateEmail();
    $userPassword = _validatePassword();
    require_once __DIR__."/../db.php";
    $sql = "SELECT * FROM users WHERE user_email = :email AND deleted_at IS NULL";
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
    if(!$user){
        header("Location: /");
        exit();
    }
    if( !password_verify($userPassword, $user["user_password"])){
        header("Location: /");
        exit();
    };
    unset($user["user_password"]);
    session_start();
    $_SESSION["user"] = $user;
    header("Location: /home");

}catch(Exception $e){
    http_response_code($e->getCode());
    _($e->getMessage());
}

