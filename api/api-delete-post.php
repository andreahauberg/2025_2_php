<?php
session_start();
require_once __DIR__ . '/../x.php';
// sørg for at brugeren er logget ind
_ensureLogin('/');

try {
    // Accept POST (from fetch) or GET (fallback)
    $postPk = $_POST['post_pk'] ?? $_GET['post_pk'] ?? null;

    // bestem hvor der skal redirectes hen via helper funktion i x.php
    $redirect = _redirectPath('/home');

    if (!$postPk) {
        throw new Exception("Post ID is required", 400);
    }

    require_once __DIR__ . '/../db.php';

     // Tjek om posten tilhører brugeren
    $checkSql = "SELECT 1 
                 FROM posts 
                 WHERE post_pk = :postPk 
                   AND post_user_fk = :userPk 
                   AND deleted_at IS NULL";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->execute([
        ':postPk' => $postPk,
        ':userPk' => $_SESSION["user"]["user_pk"]
    ]);

    if (!$checkStmt->fetchColumn()) {
        throw new Exception("You do not have permission to delete this post.", 403);
    }

    // Slet posten (soft delete)
    $sql = "UPDATE posts SET deleted_at = NOW() WHERE post_pk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->execute([':postPk' => $postPk]);

    _toastOk('Post deleted');
    header("Location: " . $redirect);
    exit;
} catch (Exception $e) {
    _toastError($e->getMessage());
    header("Location: " . ($redirect ?? '/home'));
    exit;
}