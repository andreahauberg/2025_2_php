<?php

session_start();
$user = $_SESSION["user"];

if (!$user) {
    header("Location: /login?message=Please login to unlike a post");
    exit;
}

try {
    require_once __DIR__ . '/../x.php';

    $userPk = $user['user_pk'];
    $postPk = _validatePk('post_pk');

    require_once __DIR__ . '/../db.php';
    $sql = "DELETE FROM likes WHERE like_user_fk = :userPk AND like_post_fk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':userPk', $userPk);
    $stmt->bindParam(':postPk', $postPk);
    $stmt->execute();

    header("Location: /home");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
