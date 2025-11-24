<?php

try {
    require_once __DIR__ . "/../x.php";
    require_once __DIR__ . "/../classes/User.php";
    $userFullName = _validateUserFullName();
    $username = _validateUsername();
    $userEmail = _validateEmail();
    $userPassword = _validatePassword();
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    $userPk = bin2hex(random_bytes(25));

    require_once __DIR__ . "/../db.php";
    // Use the User class to create a new user. Example of using OOP
    User::create($_db, $userPk, $username, $userFullName, $userEmail, $hashedPassword);

    header("Location: /?message=" . urlencode("Account created successfully! Please login."));
    exit();
} catch (Exception $e) {
    http_response_code($e->getCode());
    _($e->getMessage());
}
