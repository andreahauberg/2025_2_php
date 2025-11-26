<?php
session_start();
require_once __DIR__ . '/../x.php';
// ensure user is logged in
_ensureLogin('/');

try {

    // valider input
    $postPk      = $_POST['post_pk'] ?? null;
    $postMessage = _validatePost(); 

    if (!$postPk) {
        throw new Exception("Post ID is required", 400);
    }

    require_once __DIR__ . '/../db.php';

    // Tjek om brugeren ejer posten - hent kun post_message så fetchColumn() returnerer selve teksten (så vi kan tjekke for ændringer ift toast)
    $checkSql = "SELECT post_message FROM posts WHERE post_pk = :postPk AND post_user_fk = :userPk AND deleted_at IS NULL";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->execute([':postPk' => $postPk, ':userPk' => $_SESSION['user']['user_pk']]);

    $dbMessage = $checkStmt->fetchColumn();

    if ($dbMessage === false) {
        throw new Exception("You do not have permission to update this post.", 403);
    }

    // bestem hvor der skal redirectes hen via helper funktion i x.php
    $redirect = _redirectPath('/home');


    // ingen ændring -> toast + genåbn dialog
    if (trim((string)$dbMessage) === trim((string)$postMessage)) {
        _toastError('Please change something to update your post');
        $_SESSION['open_dialog']             = 'update';
        $_SESSION['old_update_post_pk']      = $postPk;
        $_SESSION['old_update_post_message'] = $postMessage;

        header("Location: " . $redirect);
        exit();
    }

    // opdater post
    $sql = "UPDATE posts SET post_message = :postMessage, updated_at = NOW() WHERE post_pk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':postMessage', $postMessage);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();
    
    // Success toast
    _toastOk('Post updated!');
    header("Location: " . $redirect);
    exit();

} catch (Exception $e) {

    // Error toast
    _toastError($e->getMessage());
    header("Location: " . ($redirect ?? '/home'));
    exit();
}
