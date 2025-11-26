<?php
session_start();
// user skal være logged in for at delete en post
if (!isset($_SESSION["user"])) {
    $_SESSION['toast'] = [
        'message' => 'Please login to delete your post',
        'type'    => 'error'
    ];
    header("Location: /");
    exit();
}

try {
    require_once __DIR__ . '/../x.php';
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

    $_SESSION['toast'] = [
        'message' => 'Post deleted',
        'type'    => 'ok'
    ];

    header("Location: " . $redirect);
    exit;
} catch (Exception $e) {
    $_SESSION['toast'] = [
        'message' => $e->getMessage(),
        'type'    => 'error'
    ];
    header("Location: " . ($redirect ?? '/home'));
    exit;
}