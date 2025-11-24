<?php

try {
    
    require_once __DIR__ . "/../db.php";
    
    session_start();
    
    if (!isset($_SESSION["user"])) {
        http_response_code(401);
        header("Location: /?message=error");
        exit;
    }
    
    session_destroy();
    $user_id = $_SESSION["user"]["user_pk"];

    $sql = "UPDATE users SET deleted_at = NOW() WHERE user_pk = :user_pk";
    $stmt = $_db->prepare($sql);
    $stmt->bindValue(":user_pk", $user_id);
    $stmt->execute();

    header("Location: /?message=profile deleted");

} catch (Exception $e) {
    http_response_code(500);
    echo "error";
}