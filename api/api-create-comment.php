<?php
require_once __DIR__ . '/../x.php';   // <--- først: få helper-funktionerne ind
_ensureLogin();                    // <--- tjek login (starter selv session ved behov)

try {
    require_once __DIR__ . '/../db.php';

    $postPk         = _validatePk('post_pk');
    $commentMessage = _validateComment();

    // tjek at posten findes
    $q = "SELECT 1 FROM posts WHERE post_pk = :postPk AND deleted_at IS NULL";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();
    if (!$stmt->fetchColumn()) {
        throw new Exception("Post not found", 404);
    }

    $commentPk = bin2hex(random_bytes(25));

    $sql = "
      INSERT INTO comments
        (comment_pk, comment_post_fk, comment_user_fk, comment_message)
      VALUES
        (:comment_pk, :post_pk, :user_pk, :message)
    ";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':comment_pk', $commentPk);
    $stmt->bindValue(':post_pk',   $postPk);
    $stmt->bindValue(':user_pk',   $_SESSION["user"]["user_pk"]);
    $stmt->bindValue(':message',   $commentMessage);

    // TJEK OM INSERT LYKKES
    if (!$stmt->execute() || $stmt->rowCount() !== 1) {
        $info = $stmt->errorInfo();
        throw new Exception("Could not create comment: " . ($info[2] ?? 'DB error'), 500);
    }

    // hent den indsatte kommentar
    $q = "
        SELECT c.comment_pk,
               c.comment_message,
               c.comment_created_at,
               c.updated_at,
               c.comment_user_fk,
               u.user_full_name,
               u.user_username
        FROM comments c
        LEFT JOIN users u ON u.user_pk = c.comment_user_fk
        WHERE c.comment_pk = :comment_pk
        LIMIT 1
    ";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':comment_pk', $commentPk);
    $stmt->execute();
    $inserted = $stmt->fetch();

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        'success' => true,
        'message' => 'Kommentar oprettet',
        'comment' => $inserted
    ]);
} catch (Exception $e) {
    error_log("[api-create-comment] " . $e->getMessage());
    try { _toastError($e->getMessage()); } catch (Exception $_) {}

    http_response_code($e->getCode() >= 400 ? $e->getCode() : 400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'message' => $e->getMessage()
    ]);
}
