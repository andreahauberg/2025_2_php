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

    $sql = "UPDATE users SET deleted_at = NOW() WHERE user_pk = :user_pk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    // destroy the existing session to log the user out
    session_destroy();

    // use toast helper to notify and redirect (will start a fresh session for the toast)
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