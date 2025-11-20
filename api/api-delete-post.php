<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: /?message=Please login to delete your post");
    exit;
}

try {
    require_once __DIR__ . '/../x.php';
    $postPk = $_GET['post_pk'] ?? null;

    if (!$postPk) {
        throw new Exception("Post ID is required.");
    }

    require_once __DIR__ . '/../db.php';

    // Tjek om posten tilhører brugeren
    $checkSql = "SELECT * FROM posts WHERE post_pk = :postPk AND post_user_fk = :userPk";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindValue(':postPk', $postPk);
    $checkStmt->bindValue(':userPk', $_SESSION["user"]["user_pk"]);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        throw new Exception("You do not have permission to delete this post.");
    }

    // Slet posten
    $sql = "DELETE FROM posts WHERE post_pk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':postPk', $postPk);
    $stmt->execute();

    header("Location: /home");
} catch (Exception $e) {
    header("Location: /home?message=" . urlencode($e->getMessage()));
}
