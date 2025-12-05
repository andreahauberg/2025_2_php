<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated", "unread_count" => 0]);
    exit();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/NotificationModel.php';

try {
    $nm = new NotificationModel();
    $count = $nm->countUnreadForUser($_SESSION['user']['user_pk']);

    echo json_encode(["success" => true, "unread_count" => $count]);
    exit();
} catch (Exception $ex) {
    echo json_encode(["success" => false, "message" => $ex->getMessage(), "unread_count" => 0]);
    exit();
}
