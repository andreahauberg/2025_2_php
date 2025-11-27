<?php
session_start();

_ensureLogin('/');  

try {
    require_once __DIR__ . '/../x.php';
    require_once __DIR__ . '/../db.php';

    $commentPk      = _validatePk('comment_pk');
    $commentMessage = _validateComment();

    // Tjek ejerskab
    $checkSql = "
      SELECT 1
      FROM comments
      WHERE comment_pk = :commentPk
        AND comment_user_fk = :userPk
        AND deleted_at IS NULL
    ";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->execute([
        ':commentPk' => $commentPk,
        ':userPk'    => $_SESSION["user"]["user_pk"]
    ]);
    if (!$checkStmt->fetchColumn()) {
        throw new Exception("You do not have permission to update this comment.", 403);
    }

    // Opdater
    $sql = "
      UPDATE comments
      SET comment_message = :message, updated_at = NOW()
      WHERE comment_pk = :commentPk
    ";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':message',   $commentMessage);
    $stmt->bindValue(':commentPk', $commentPk);
    $stmt->execute();

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("Content-Type: application/json; charset=utf-8");
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
