<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: /?message=Please login to update your post");
    exit;
}

try {
    require_once __DIR__ . '/../x.php';
    $postPk = $_POST['post_pk'] ?? null;
    $postMessage = _validatePost();

    if (!$postPk) {
        throw new Exception("Post ID is required.");
    }

    require_once __DIR__ . '/../db.php';

    // Tjek om brugeren ejer posten
    $checkSql = "SELECT * FROM posts WHERE post_pk = :postPk AND post_user_fk = :userPk AND deleted_at IS NULL";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindValue(':postPk', $postPk);
    $checkStmt->bindValue(':userPk', $_SESSION["user"]["user_pk"]);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        throw new Exception("You do not have permission to update this post.");
    }

    // Opdater posten
    $sql = "UPDATE posts SET post_message = :postMessage, updated_at = NOW() WHERE post_pk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':postMessage', $postMessage);
    $stmt->bindParam(':postPk', $postPk);
    $stmt->execute();

    header("Location: /home");
} catch (Exception $e) {
    header("Location: /home?message=" . urlencode($e->getMessage()));
}
