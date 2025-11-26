<?php
session_start();

require_once __DIR__ . '/../x.php';
_ensureLogin('/');              // redirects + toast if not logged in

require_once __DIR__ . '/../db.php';

// read & clamp offset/limit
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limit  = isset($_GET['limit'])  ? (int) $_GET['limit']  : 2;

$offset = max(0, $offset);
$limit  = max(1, min(10, $limit));

$sql = "
  SELECT 
    LEFT(post_message, 40) AS topic,
    COUNT(*) AS post_count
  FROM posts
  GROUP BY topic
  ORDER BY post_count DESC
  LIMIT :offset, :limit
";

$stmt = $_db->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
