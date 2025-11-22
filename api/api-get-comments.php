<?php
session_start();
if (!isset($_SESSION["user"])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
try {
    require_once __DIR__ . '/../db.php';
    $postPk = $_GET['post_pk'] ?? null;
    if (!$postPk) {
        throw new Exception("Post ID is required.");
    }
    $sql = "
        SELECT
            comments.comment_pk,
            comments.comment_message,
            comments.comment_created_at,
            comments.updated_at,
            comments.comment_user_fk,
            users.user_full_name,
            users.user_username
        FROM comments
        JOIN users ON comments.comment_user_fk = users.user_pk
        WHERE comments.comment_post_fk = :postPk AND comments.deleted_at IS NULL
        ORDER BY comments.comment_created_at DESC
    ";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($comments);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
