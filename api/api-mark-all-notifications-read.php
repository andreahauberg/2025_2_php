<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['user'])){
    echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false,'message'=>'Invalid method']);
    exit();
}
require_once __DIR__ . '/../db.php';
try{
    $q = "UPDATE notifications SET is_read = 1 WHERE notification_user_fk = :user AND is_read = 0 AND notification_post_fk IS NOT NULL";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':user', $_SESSION['user']['user_pk']);
    $stmt->execute();
    $rows = $stmt->rowCount();
    echo json_encode(['success'=>true,'updated'=>$rows]);
    exit();
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit();
}
