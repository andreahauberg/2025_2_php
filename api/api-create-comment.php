<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
try {
    require_once __DIR__ . '/../db.php';
    $postPk = $_POST['post_pk'] ?? null;
    $commentMessage = trim($_POST['comment_message'] ?? '');
    if (!$postPk || strlen($commentMessage) < 1 || strlen($commentMessage) > 300) {
        throw new Exception("Invalid comment data.");
    }
    $commentPk = bin2hex(random_bytes(25));
    $sql = "INSERT INTO comments (comment_pk, comment_post_fk, comment_user_fk, comment_message) VALUES (:comment_pk, :post_pk, :user_pk, :message)";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':comment_pk', $commentPk);
    $stmt->bindValue(':post_pk', $postPk);
    $stmt->bindValue(':user_pk', $_SESSION["user"]["user_pk"]);
    $stmt->bindValue(':message', $commentMessage);
    $stmt->execute();
    header("Content-Type: application/json");
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(['error' => $e->getMessage()]);
}
