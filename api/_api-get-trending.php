<?php
session_start();

require_once __DIR__ . '/../x.php';
_ensureLogin('/');              // redirects + toast if not logged in

require_once __DIR__ . '/../db.php';

// read & clamp offset/limit
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limit  = isset($_GET['limit'])  ? (int) $_GET['limit']  : 2;

// 1. Hent posts der indeholder hashtags
$sql = "
    SELECT post_message
    FROM posts
    WHERE post_message REGEXP '#[A-Za-z0-9_]+'
";
$stmt = $_db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = [];

foreach ($rows as $row) {
    preg_match_all('/#[A-Za-z0-9_]+/', $row['post_message'], $matches);
    foreach ($matches[0] as $tag) {
        $tagLower = strtolower($tag);
        if (!isset($counts[$tagLower])) {
            $counts[$tagLower] = 0;
        }
        $counts[$tagLower]++;
    }
}

arsort($counts);

$tags = array_keys($counts);
$chunk = array_slice($tags, $offset, $limit);

$out = [];
foreach ($chunk as $tag) {
    $out[] = [
        "topic"      => $tag,
        "post_count" => $counts[$tag]
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);