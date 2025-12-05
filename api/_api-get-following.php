<?php
session_start();
require_once __DIR__ . '/../x.php';
_ensureLogin('/');
require_once __DIR__ . '/../db.php';

$currentUser = _currentUser();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit  = isset($_GET['limit'])  ? (int)$_GET['limit']  : 10;

$sql = "
    SELECT u.user_pk, u.user_full_name, u.user_username, u.user_avatar
    FROM follows f
    JOIN users u ON u.user_pk = f.follow_user_fk
    WHERE f.follower_user_fk = :user
      AND u.deleted_at IS NULL
    ORDER BY f.created_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $_db->prepare($sql);
$stmt->bindValue(':user', $currentUser['user_pk']);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
