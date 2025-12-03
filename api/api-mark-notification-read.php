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
$pk = $_POST['notification_pk'] ?? null;
if (!$pk){
    echo json_encode(['success'=>false,'message'=>'Missing notification_pk']);
    exit();
}
require_once __DIR__ . '/../db.php';
try{
    $q = "UPDATE notifications SET is_read = 1 WHERE notification_pk = :pk AND notification_user_fk = :user";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':pk', $pk);
    $stmt->bindValue(':user', $_SESSION['user']['user_pk']);
    $stmt->execute();
    $rows = $stmt->rowCount();
    echo json_encode(['success'=>true,'updated'=>$rows]);
    exit();
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit();
}
