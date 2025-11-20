<?php
try {
    require_once __DIR__ . "/../x.php";
    session_start();

    if (!isset($_SESSION["user"])) {
        header("Location: /?message=Please login first");
        exit();
    }

    require_once __DIR__ . "/../db.php";

    $query = trim($_POST["query"] ?? "");

    $sqlUsers = "
        SELECT 
            user_pk,
            user_username,
            user_full_name,
            user_email
        FROM users
        WHERE 
            user_username LIKE :q
            OR user_full_name LIKE :q
            OR user_email LIKE :q
        LIMIT 25
    ";
    $stmtUsers = $_db->prepare($sqlUsers);
    $stmtUsers->bindValue(':q', "%$query%");
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll();

    $sqlPosts = "
        SELECT 
            posts.post_pk,
            posts.post_message,
            posts.post_image_path,
            posts.post_user_fk,
            users.user_username,
            users.user_full_name
        FROM posts
        JOIN users ON posts.post_user_fk = users.user_pk
        WHERE 
            posts.post_message LIKE :q
        LIMIT 25
    ";
    $stmtPosts = $_db->prepare($sqlPosts);
    $stmtPosts->bindValue(':q', "%$query%");
    $stmtPosts->execute();
    $posts = $stmtPosts->fetchAll();

    require_once __DIR__ . "/../views/search.php";

} catch (Exception $e) {
    http_response_code($e->getCode());
    _($e->getMessage());
}