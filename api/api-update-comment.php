<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
try {
    require_once __DIR__ . '/../db.php';
    $commentPk = $_POST['comment_pk'] ?? null;
    $commentMessage = trim($_POST['comment_message'] ?? '');
    if (!$commentPk || strlen($commentMessage) < 1 || strlen($commentMessage) > 300) {
        throw new Exception("Invalid comment data.");
    }
    // Tjek om brugeren ejer kommentaren
    $checkSql = "SELECT * FROM comments WHERE comment_pk = :commentPk AND comment_user_fk = :userPk AND deleted_at IS NULL";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindValue(':commentPk', $commentPk);
    $checkStmt->bindValue(':userPk', $_SESSION["user"]["user_pk"]);
    $checkStmt->execute();
    if ($checkStmt->rowCount() === 0) {
        throw new Exception("You do not have permission to update this comment.");
    }
    // Opdater kommentaren
    $sql = "UPDATE comments SET comment_message = :message, updated_at = NOW() WHERE comment_pk = :commentPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':message', $commentMessage);
    $stmt->bindValue(':commentPk', $commentPk);
    $stmt->execute();
    header("Content-Type: application/json");
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(['error' => $e->getMessage()]);
}
