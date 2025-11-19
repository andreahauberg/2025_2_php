<?php

session_start();
$user = $_SESSION["user"];

if (!$user) {
    header("Location: /?message=Please login to like a post");
    exit;
}

try {
    require_once __DIR__ . '/../x.php';

    $userPk = $user['user_pk'];
    $postPk = _validatePk('post_pk');

    require_once __DIR__ . '/../db.php';
    $sql = "INSERT INTO likes (like_user_fk, like_post_fk) VALUES (:userPk, :postPk)";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':userPk', $userPk);
    $stmt->bindParam(':postPk', $postPk);
    $stmt->execute();

    header("Location: /home");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
