<?php
header('Content-Type: application/json; charset=utf-8');
try {
    require_once __DIR__ . "/../db.php";

    $postPk = $_GET['post_pk'] ?? null;
    if (!$postPk) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'missing_post_pk']);
        exit;
    }

    $q = "SELECT post_pk, post_message, post_user_fk FROM posts WHERE post_pk = :post_pk AND deleted_at IS NULL LIMIT 1";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':post_pk', $postPk);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'not_found']);
        exit;
    }

    echo json_encode(['success' => true, 'post' => $post]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('api-get-post error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'server_error']);
}
