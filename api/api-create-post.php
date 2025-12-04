<?php 
session_start();
require_once __DIR__."/../x.php";

_ensureLogin('/');

$user = $_SESSION["user"];

try {
    $postMessage = _validatePost();
$postImage = "";

if (!empty($_FILES["post_image"]["name"])) {
    $fileTmp = $_FILES["post_image"]["tmp_name"];
    $fileName = bin2hex(random_bytes(10)) . "_" . basename($_FILES["post_image"]["name"]);
    $targetPath = __DIR__ . "/../public/uploads/posts" . $fileName;

    if (!is_dir(__DIR__ . "/../public/uploads")) {
        mkdir(__DIR__ . "/../public/uploads", 0777, true);
    }

    if (move_uploaded_file($fileTmp, $targetPath)) {
        $postImage = "/public/uploads/posts" . $fileName;
    }
}

    $postPk = bin2hex(random_bytes(25));

    require_once __DIR__."/../db.php";
    $sql = "INSERT INTO posts (post_pk, post_message, post_image_path, post_user_fk, created_at) Values (:post_pk, :post_message, :post_image_path, :post_user_fk, NOW())";

    $stmt = $_db->prepare( $sql );

    $stmt->bindValue(":post_pk", $postPk);
    $stmt->bindValue(":post_message", $postMessage);
    $stmt->bindValue(":post_image_path", $postImage);
    $stmt->bindValue(":post_user_fk", $user["user_pk"]);

    $stmt->execute();

    try {
        require_once __DIR__ . '/../models/NotificationModel.php';
        $nm = new NotificationModel();
        $notifMessage = mb_strimwidth(strip_tags($postMessage), 0, 200, '...');
        $nm->createForFollowers($user['user_pk'], $postPk, $notifMessage);
    } catch (Exception $e) {
        error_log('[api-create-post] Notification create failed: ' . $e->getMessage());
    }

    unset($_SESSION['old_post_message']);
    $redirect = _redirectPath('/home');
    _toastRedirect('Post created!', 'ok', $redirect);
}
catch(Exception $e){
    _toastError($e->getMessage());
    $_SESSION['open_dialog'] = 'post';
    if (!empty($_POST['post_message'])) {
        $_SESSION['old_post_message'] = $_POST['post_message'];
    }
    $redirect = _redirectPath('/home');
    header('Location: ' . $redirect);
    exit();
}