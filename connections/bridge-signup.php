<?php
try{
session_start(); 
    require_once __DIR__ . "/../x.php";

    $userFullName = _validateUserFullName();
    $username = _validateUsername();
    $userEmail = _validateEmail();
    $userPassword = _validatePassword();
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    $userPk = bin2hex(random_bytes(25));

    require_once __DIR__ . "/../db.php";

    // tjek username og email er unikt
    $check = $_db->prepare('SELECT user_username, user_email FROM users WHERE user_username = :u OR user_email = :e LIMIT 1');
    $check->execute([':u' => $username, ':e' => $userEmail]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        if (isset($existing['user_username']) && $existing['user_username'] === $username) {
            $_SESSION['toast'] = ['message' => 'Username is already taken', 'type' => 'error'];
        } else {
            $_SESSION['toast'] = ['message' => 'You already have an account with this email', 'type' => 'error'];
        }
        // keep signup dialog open when toast is shown
        $_SESSION['open_dialog'] = 'signup';
        header('Location: /');
        exit();
    }

    $sql = "INSERT INTO users (user_pk, user_username, user_full_name, user_email, user_password) VALUES (:user_pk, :user_username, :full_name, :email, :password)";
    $stmt = $_db->prepare( $sql );

    $stmt->bindValue(":user_pk", $userPk);
    $stmt->bindValue(":user_username", $username);
    $stmt->bindValue(":full_name", $userFullName);
    $stmt->bindValue(":email", $userEmail);
    $stmt->bindValue(":password", $hashedPassword);

    $stmt->execute();

    // success: vis toast og redirect to login
    $_SESSION['toast'] = ['message' => 'Account created successfully! Please login.', 'type' => 'ok'];
    $_SESSION['open_dialog'] = 'login';
    header('Location: /');
    exit();

}
catch(Exception $e){
    $_SESSION['toast'] = ['message' => $e->getMessage(), 'type' => 'error'];
    $_SESSION['open_dialog'] = 'signup';
    header('Location: /');
    exit();
}