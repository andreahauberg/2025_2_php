<?php 
session_start();

require_once __DIR__."/../x.php";

$user = $_SESSION["user"];

if (!$user) {
    header("Location: /?message=not logged in, please login first");
    exit;
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
    $stmt->bindValue("post_user_fk", $user["user_pk"]);

    $stmt->execute();

    header("Location: /home?message=" . urlencode("Post created!"));
    exit();
}
catch(Exception $e){
    http_response_code($e->getCode());
    echo $e->getMessage();
}