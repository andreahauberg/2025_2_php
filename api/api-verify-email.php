<?php
session_start();
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../x.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    _toastError("Invalid verification link");
    header("Location: /");
    exit();
}

$stmt = $_db->prepare("
    SELECT user_pk 
    FROM users 
    WHERE user_verify_token = :t 
    AND user_is_verified = 0
    LIMIT 1
");
$stmt->bindValue(':t', $token);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    _toastError("Invalid or expired verification token");
    header("Location: /");
    exit();
}

$stmt = $_db->prepare("
    UPDATE users
    SET user_is_verified = 1,
        user_verify_token = NULL
    WHERE user_pk = :pk
");
$stmt->bindValue(':pk', $user['user_pk']);
$stmt->execute();

_toastOk("Your email is verified! You may now log in.");
$_SESSION['open_dialog'] = 'login';
header("Location: /");
exit();