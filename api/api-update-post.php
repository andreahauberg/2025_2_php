<?php
session_start();

// user skal være logged in for at opdatere en post
if (!isset($_SESSION["user"])) {
    $_SESSION['toast'] = [
        'message' => 'Please login to update your post',
        'type'    => 'error'
    ];
    header("Location: /");
    exit();
}

try {
    require_once __DIR__ . '/../x.php';

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

    // ingen ændring -> toast + genåbn dialog
    if (trim((string)$dbMessage) === trim((string)$postMessage)) {
        $_SESSION['toast'] = [
            'message' => 'Please change something to update your post',
            'type'    => 'error'
        ];
        $_SESSION['open_dialog']             = 'update';
        $_SESSION['old_update_post_pk']      = $postPk;
        $_SESSION['old_update_post_message'] = $postMessage;

        header("Location: /home");
        exit();
    }

    // opdater post
    $sql = "UPDATE posts SET post_message = :postMessage, updated_at = NOW() WHERE post_pk = :postPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(':postMessage', $postMessage);
    $stmt->bindValue(':postPk', $postPk);
    $stmt->execute();
    
    // Success toast
    $_SESSION['toast'] = [
        'message' => 'Post updated!',
        'type'    => 'ok'
    ];

    header("Location: /home");
    exit();

} catch (Exception $e) {

    // Error toast
    $_SESSION['toast'] = [
        'message' => $e->getMessage(),
        'type'    => 'error'
    ];

    header("Location: /home");
    exit();
}
