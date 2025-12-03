<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated", "unread_count" => 0]);
    exit();
}

require_once __DIR__ . '/../db.php';

try {
    $q = "SELECT COUNT(*) AS cnt FROM notifications WHERE notification_user_fk = :user AND is_read = 0 AND notification_post_fk IS NOT NULL";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':user', $_SESSION['user']['user_pk']);
    $stmt->execute();
    $row = $stmt->fetch();
    $count = $row ? (int)$row['cnt'] : 0;

    echo json_encode(["success" => true, "unread_count" => $count]);
    exit();
} catch (Exception $ex) {
    echo json_encode(["success" => false, "message" => $ex->getMessage(), "unread_count" => 0]);
    exit();
}
