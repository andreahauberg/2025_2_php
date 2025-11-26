<?php
try{
session_start(); 
    require_once __DIR__ . "/../x.php";
    require_once __DIR__ . "/../classes/User.php";

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
    $existing = $check->fetch();
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

    // Use the User class to create a new user. Example of using OOP
    User::create($_db, $userPk, $username, $userFullName, $userEmail, $hashedPassword);

    // ved succesfuld oprettelse, log brugeren ind automatisk
    $fetch = $_db->prepare('SELECT * FROM users WHERE user_pk = :pk AND deleted_at IS NULL LIMIT 1');
    $fetch->execute([':pk' => $userPk]);
    $newUser = $fetch->fetch();
    if ($newUser) {
        unset($newUser['user_password']);
        $_SESSION['user'] = $newUser;
        $_SESSION['toast'] = ['message' => 'Welcome, ' . ($newUser['user_full_name'] ?? $username) . '!', 'type' => 'ok'];
        header('Location: /home');
        exit();
    } else {
        // fallback
        $_SESSION['toast'] = ['message' => 'Account created successfully! Please login.', 'type' => 'ok'];
        $_SESSION['open_dialog'] = 'login';
        header('Location: /');
        exit();
    }

}
catch(Exception $e){
    $_SESSION['toast'] = ['message' => $e->getMessage(), 'type' => 'error'];
    $_SESSION['open_dialog'] = 'signup';
    header('Location: /');
    exit();
}