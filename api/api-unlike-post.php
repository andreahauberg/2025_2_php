<?php
try {
    session_start();
    if (!isset($_SESSION["user"])) {
        http_response_code(401);
        echo "Please login to unlike a post";
        exit;
    }

    require_once __DIR__ . '/../x.php';
    require_once __DIR__ . '/../db.php';

    $userPk = $_SESSION["user"]["user_pk"];
    $postPk = $_GET['post_pk'];

    // Slet like fra databasen
    $sql = "DELETE FROM likes WHERE like_user_fk = :userPk AND like_post_fk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':userPk', $userPk);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();

    echo "Post unliked";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}