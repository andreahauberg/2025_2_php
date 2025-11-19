<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(401);
    echo json_encode(["error" => "Please login to unfollow a user"]);
    exit;
}

try {
    require_once __DIR__ . '/../x.php';
    $followerPk = $_SESSION["user"]["user_pk"];
    $followPk = $_GET['user-pk'];

    require_once __DIR__ . '/../db.php';

    // Slet follow-relationen
    $sql = "DELETE FROM follows WHERE follower_user_fk = :followerPk AND follow_user_fk = :followPk";
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':followerPk', $followerPk);
    $stmt->bindParam(':followPk', $followPk);
    $stmt->execute();

    // Returner den nye follow-knap
    $user_pk = $followPk;
    echo "<mixhtml mix-replace='.button-$user_pk'>";
    require __DIR__ . '/../components/___button_follow.php';
    echo "</mixhtml>";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

