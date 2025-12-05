<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(401);
    echo json_encode(["error" => "Please login to follow a user"]);
    exit;
}

try {
    require_once __DIR__ . '/../x.php';
    $followerPk = $_SESSION["user"]["user_pk"];
    $followPk = $_GET['user-pk'];

    if ($followerPk === $followPk) {
        http_response_code(400);
        echo json_encode(["error" => "You cannot follow yourself"]);
        exit;
    }

    require_once __DIR__ . '/../db.php';

    // verify follow target exists and is not soft-deleted
    $userSql = "SELECT user_pk FROM users WHERE user_pk = :followPk AND deleted_at IS NULL LIMIT 1";
    $userStmt = $_db->prepare($userSql);
    $userStmt->bindValue(':followPk', $followPk);
    $userStmt->execute();

    if (!$userStmt->fetchColumn()) {
        // user doesn't exist or is soft-deleted
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'    => false,
            'error'      => 'User was deleted',
            'error_code' => 'user_deleted',
            'user_pk'    => $followPk,
        ]);
        exit();
    }

    // Tjek om brugeren allerede følger
    $checkSql = "SELECT * FROM follows WHERE follower_user_fk = :followerPk AND follow_user_fk = :followPk";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindParam(':followerPk', $followerPk);
    $checkStmt->bindParam(':followPk', $followPk);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(["error" => "Already following this user"]);
        exit;
    }

    // Indsæt follow-relationen
    $sql = "INSERT INTO follows (follower_user_fk, follow_user_fk) VALUES (:followerPk, :followPk)";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':followerPk', $followerPk);
    $stmt->bindParam(':followPk', $followPk);
    $stmt->execute();

    // Returner den nye unfollow-knap
    $user_pk = $followPk;
    echo "<mixhtml mix-replace='.button-$user_pk'>";
    require __DIR__ . '/../components/___button_unfollow.php';
    echo "</mixhtml>";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
