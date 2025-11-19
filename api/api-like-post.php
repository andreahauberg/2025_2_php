<?php
try {
    session_start();
    if (!isset($_SESSION["user"])) {
        http_response_code(401);
        echo "Please login to like a post";
        exit;
    }

    require_once __DIR__ . '/../x.php';
    require_once __DIR__ . '/../db.php';

    $userPk = $_SESSION["user"]["user_pk"];
    $postPk = $_GET['post_pk'];

    // Tjek om brugeren allerede har liket posten
    $checkSql = "SELECT * FROM likes WHERE like_user_fk = :userPk AND like_post_fk = :postPk";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindValue(':userPk', $userPk);
    $checkStmt->bindValue(':postPk', $postPk);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo "Post already liked";
        exit;
    }

    // Indsæt like i databasen
    $sql = "INSERT INTO likes (like_user_fk, like_post_fk) VALUES (:userPk, :postPk)";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':userPk', $userPk);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();

    echo "Post liked";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}