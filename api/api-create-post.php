<?php 
session_start();
require_once __DIR__."/../x.php";

// ensure user logged in
_ensureLogin('/');

// current user from session
$user = $_SESSION["user"];

try {
    $postMessage = _validatePost();
    $postImage = "https://picsum.photos/400/250";

    $postPk = bin2hex(random_bytes(25));

    require_once __DIR__."/../db.php";
    $sql = "INSERT INTO posts (post_pk, post_message, post_image_path, post_user_fk, created_at) Values (:post_pk, :post_message, :post_image_path, :post_user_fk, NOW())";

    $stmt = $_db->prepare( $sql );

    $stmt->bindValue(":post_pk", $postPk);
    $stmt->bindValue(":post_message", $postMessage);
    $stmt->bindValue(":post_image_path", $postImage);
    $stmt->bindValue(":post_user_fk", $user["user_pk"]);

    $stmt->execute();

    
    // success: åben ikke dialog boksen igen 
    unset($_SESSION['old_post_message']);
    $redirect = _redirectPath('/home');
    _toastRedirect('Post created!', 'ok', $redirect);
}
catch(Exception $e){
    _toastError($e->getMessage());
    $_SESSION['open_dialog'] = 'post';
    // behold den gamle post besked 
    if (!empty($_POST['post_message'])) {
        $_SESSION['old_post_message'] = $_POST['post_message'];
    }
    $redirect = _redirectPath('/home');
    header('Location: ' . $redirect);
    exit();
}