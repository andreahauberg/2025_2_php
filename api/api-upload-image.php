<?php
session_start();
require_once __DIR__ . '/../x.php';
_ensureLogin('/');
require_once __DIR__ . '/../db.php';

$currentUser = $_SESSION["user"];
$userPk = $currentUser["user_pk"];

$type = $_POST['type'] ?? null;
if (!in_array($type, ['avatar', 'cover'])) {
    _toastError("Invalid upload type");
    header("Location: /profile");
    exit();
}

if (!isset($_FILES['file'])) {
    _toastError("No file received");
    header("Location: /profile");
    exit();
}

$error = $_FILES['file']['error'];

if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
    _toastError("Image too large — max size 3 MB");
    header("Location: /profile");
    exit();
}

if ($error !== UPLOAD_ERR_OK) {
    _toastError("Upload failed");
    header("Location: /profile");
    exit();
}

$file = $_FILES['file'];

$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowed)) {
    _toastError("Invalid file type — allowed: JPG, PNG, WEBP");
    header("Location: /profile");
    exit();
}

$maxSize = 3 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    _toastError("Image too large — max size 3 MB");
    header("Location: /profile");
    exit();
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = bin2hex(random_bytes(12)) . "." . $ext;

$folder = $type === 'avatar' ? 'avatars' : 'covers';

$uploadDir = __DIR__ . "/../public/uploads/{$folder}/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$targetPath = $uploadDir . $newName;
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    _toastError("Upload failed");
    header("Location: /profile");
    exit();
}

$publicPath = "/public/uploads/{$folder}/{$newName}";

$field = $type === 'avatar' ? 'user_avatar' : 'user_cover';

$q = "UPDATE users SET {$field} = :img WHERE user_pk = :pk LIMIT 1";
$stmt = $_db->prepare($q);
$stmt->bindValue(':img', $publicPath);
$stmt->bindValue(':pk', $userPk);
$stmt->execute();

$_SESSION["user"][$field] = $publicPath;

_toastRedirect("Image updated", "ok", "/profile");
header("Location: /profile");
exit();