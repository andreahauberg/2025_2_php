<?php
session_start();
require_once __DIR__.'/../x.php';
require_once __DIR__.'/../db.php';

try {
    $email    = _validateEmail();
    $password = _validatePassword();

    $stmt = $_db->prepare("
        SELECT * 
        FROM users 
        WHERE user_email = :email 
          AND deleted_at IS NULL
        LIMIT 1
    ");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        _toastError("There is no account with this email");
        $_SESSION['open_dialog'] = 'login';
        header("Location: /");
        exit();
    }

    if (!password_verify($password, $user['user_password'])) {
        _toastError("Incorrect password");
        $_SESSION['open_dialog'] = 'login';
        header("Location: /");
        exit();
    }
    
    if (!$user["user_is_verified"] && weaveIsProd()) {
        _toastError("Please verify your email before logging in");
        $_SESSION['open_dialog'] = 'login';
        header("Location: /");
        exit();
    }

    unset($user['user_password']);
    $_SESSION['user'] = $user;

    header("Location: /home");
    exit();

} catch (Exception $e) {
    _toastError($e->getMessage());
    $_SESSION['open_dialog'] = 'login';
    header("Location: /");
    exit();
}