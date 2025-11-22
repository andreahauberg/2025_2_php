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
    if (!$commentPk) {
        throw new Exception("Comment ID is required.");
    }
    // Tjek om brugeren ejer kommentaren
    $checkSql = "SELECT * FROM comments WHERE comment_pk = :commentPk AND comment_user_fk = :userPk AND deleted_at IS NULL";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindValue(':commentPk', $commentPk);
    $checkStmt->bindValue(':userPk', $_SESSION["user"]["user_pk"]);
    $checkStmt->execute();
    if ($checkStmt->rowCount() === 0) {
        throw new Exception("You do not have permission to delete this comment.");
    }
    // Soft delete kommentaren
    $sql = "UPDATE comments SET deleted_at = NOW() WHERE comment_pk = :commentPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':commentPk', $commentPk);
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(['error' => $e->getMessage()]);
}
