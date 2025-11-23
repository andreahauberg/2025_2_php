<?php 
//session_start();
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__."/../x.php";

$user = $_SESSION["user"];

if (!$user) {
    $_SESSION['toast'] = ['message' => 'Not logged in, please login first', 'type' => 'error'];
    header("Location: /");
    exit();
} 

try {
    $postMessage = _validatePost();
    $postImage = "https://picsum.photos/400/250";

    $postPk = bin2hex(random_bytes(25));

    require_once __DIR__."/../db.php";
    $sql = "INSERT INTO posts (post_pk, post_message, post_image_path, post_user_fk) Values (:post_pk, :post_message, :post_image_path, :post_user_fk)";

    $stmt = $_db->prepare( $sql );

    $stmt->bindValue(":post_pk", $postPk);
    $stmt->bindValue(":post_message", $postMessage);
    $stmt->bindValue(":post_image_path", $postImage);
    $stmt->bindValue(":post_user_fk", $user["user_pk"]);

    $stmt->execute();

    
    $_SESSION['toast'] = ['message' => 'Post created!', 'type' => 'ok'];
    // success: åben ikke dialog boksen igen 
    unset($_SESSION['old_post_message']);
    header("Location: /home");
    exit();
}
catch(Exception $e){
    $_SESSION['toast'] = ['message' => $e->getMessage(), 'type' => 'error'];
    $_SESSION['open_dialog'] = 'post';
    // behold den gamle post besked 
    if (!empty($_POST['post_message'])) {
        $_SESSION['old_post_message'] = $_POST['post_message'];
    }
    header('Location: /home');
    exit();
}