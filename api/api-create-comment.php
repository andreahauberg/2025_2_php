<?php
session_start();

_ensureLogin('/');

try {
    require_once __DIR__ . '/../x.php';
    require_once __DIR__ . '/../db.php';

    // valider input
    $postPk         = _validatePk('post_pk');      // tjek at post_pk ser fornuftig ud
    $commentMessage = _validateComment();          // min/max length osv.

    // tjek at posten findes og ikke er slettet
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
    $stmt->execute();

    // Sæt en server-side toast, så besked vises ved næste page-load
    //_toastOk('Kommentar oprettet');

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(['success' => true, 'message' => 'Kommentar oprettet']);
} catch (Exception $e) {
    // log for debugging
    error_log("[api-create-comment] " . $e->getMessage());
    // Sæt server-side fejl-toast så brugeren ser beskeden ved næste load
    try {
        _toastError($e->getMessage());
    } catch (Exception $_) {
        // ignore
    }
    header("Content-Type: application/json; charset=utf-8");
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 400);
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'message' => $e->getMessage()]);
}
