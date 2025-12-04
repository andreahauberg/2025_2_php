<?php
session_start();
require_once __DIR__ . '/../x.php';
_ensureLogin('/');
require_once __DIR__ . '/../db.php';

$currentUser = $_SESSION["user"];
$userPk = $currentUser["user_pk"];

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    _toastError("No file uploaded");
    header("Location: /profile");
    exit();
}

$file = $_FILES['avatar'];

$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowed)) {
    _toastError("Invalid file type");
    header("Location: /profile");
    exit();
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = bin2hex(random_bytes(12)) . "." . $ext;

$uploadDir = __DIR__ . '/../public/uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$targetPath = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    _toastError("Upload failed");
    header("Location: /profile");
    exit();
}

$publicPath = "/public/uploads/avatars/" . $newName;

$q = "UPDATE users SET user_avatar = :avatar WHERE user_pk = :pk LIMIT 1";
$stmt = $_db->prepare($q);
$stmt->bindValue(':avatar', $publicPath);
$stmt->bindValue(':pk', $userPk);
$stmt->execute();
$_SESSION["user"]["user_avatar"] = $publicPath;

_toastRedirect("Profile picture updated!", "ok", "/profile");
header("Location: /profile");
exit();