<?php
session_start();
require_once __DIR__ . '/../x.php';
require_once __DIR__ . '/../db.php';


$userPk = isset($_GET['user_pk']) ? trim((string)$_GET['user_pk']) : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit  = isset($_GET['limit'])  ? (int)$_GET['limit']  : 10;

// validate
$offset = max(0, $offset);
$limit = max(1, min(100, $limit));

if ($userPk === '') {
    http_response_code(400);
    echo json_encode([]);
    exit();
}

$sql = "
    SELECT u.user_pk, u.user_full_name, u.user_username, u.user_avatar
    FROM follows f
    JOIN users u ON u.user_pk = f.follower_user_fk
    WHERE f.follow_user_fk = :user
      AND u.deleted_at IS NULL
    ORDER BY u.user_full_name ASC
    LIMIT :offset, :limit
";

$stmt = $_db->prepare($sql);
$stmt->bindValue(':user', $userPk, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);

