<?php

try {
    
    require_once __DIR__ . "/../db.php";
    require_once __DIR__ . "/../x.php";


    $user = _currentUser();
    if (empty($user) || !isset($user['user_pk'])) {
        http_response_code(401);
        header("Location: /?message=error");
        exit();
    }

    $user_id = $user['user_pk'];

    // 1) soft-delete the user
    $sql = "UPDATE users SET deleted_at = NOW() WHERE user_pk = :user_pk AND deleted_at IS NULL";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    // 2) user's posts
    $sql = "UPDATE posts SET deleted_at = NOW() WHERE post_user_fk = :user_pk AND deleted_at IS NULL";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    // 3) user's comments
    $sql = "UPDATE comments SET deleted_at = NOW() WHERE comment_user_fk = :user_pk AND deleted_at IS NULL";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    // 4) remove likes by the user 
    $sql = "DELETE FROM likes WHERE like_user_fk = :user_pk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    // log the user out
    if (session_status() !== PHP_SESSION_NONE) {
        session_unset();
        session_destroy();
    }

    // set a toast and redirect to homepage
    _toastRedirect('Profile was deleted', 'ok', '/');

} catch (Exception $e) {
    
    error_log("api-delete-profile error: " . $e->getMessage());

    if (function_exists('_toastRedirect')) {
        _toastRedirect('Could not delete profile', 'error', '/');
    } else {
        http_response_code(500);
        echo "error";
    }
}