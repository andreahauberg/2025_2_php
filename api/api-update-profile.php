<?php

session_start();
require_once __DIR__ . '/../x.php';
_ensureLogin('/');

try {
    $user = $_SESSION["user"];

    $newEmail = _validateEmail();
    $newUsername = _validateUsername();
    $newFullName = _validateUserFullName();

 
    $unchanged = (
        trim($newEmail) === trim($user['user_email']) &&
        trim($newUsername) === trim($user['user_username']) &&
        trim($newFullName) === trim($user['user_full_name'])
    );

    if ($unchanged) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Please change something before updating', 'error_code' => 'no_change']);
            exit();
        }
        _toastError('Please change something before updating');
        header('Location: /profile');
        exit();
    }

    require_once __DIR__ . '/../db.php';
    $sql = "UPDATE users SET user_email = :email, user_username = :username, user_full_name = :full_name, updated_at = NOW() WHERE user_pk = :pk AND deleted_at IS NULL";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':email', $newEmail);
    $stmt->bindParam(':username', $newUsername);
    $stmt->bindParam(':full_name', $newFullName);
    $stmt->bindParam(':pk', $user['user_pk']);
    $stmt->execute();

    $user['user_email'] = $newEmail;
    $user['user_username'] = $newUsername;
    $user['user_full_name'] = $newFullName;
    $_SESSION["user"] = $user;

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => 'Profile updated']);
        exit();
    }

    _toastRedirect('Profile updated', 'ok', '/profile');
    exit();
} catch (Exception $e) {
    if (!empty($isAjax)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
    echo "Error: " . $e->getMessage();
}
