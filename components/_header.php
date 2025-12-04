<?php
// header component includes the nav, burger menu, toast and container div
require_once __DIR__ . '/../x.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// if a page wants to force login globally, it can set $requireLogin = true before including this header
if (!empty($requireLogin) && function_exists('_ensureLogin')) {
    _ensureLogin('/');
}

// if user is logged in we try to get unread notification count if any
$notifCount = 0;
$user = _currentUser();
if ($user) {
    try {
        require_once __DIR__ . '/../models/NotificationModel.php';
        $nm = new NotificationModel();
        if (method_exists($nm, 'countUnreadForUser')) {
            $notifCount = $nm->countUnreadForUser($user['user_pk']);
        }
    } catch (Throwable $_) {
        $notifCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/public/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- common CSS -->
    <link rel="stylesheet" href="/public/css/app.css">
    <link rel="stylesheet" href="/public/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- common JS (non-critical) -->
    <script type="module" src="/public/js/app.js"></script>
    <script defer src="/public/js/dialog.js"></script>
    <script defer src="/public/js/comment.js"></script>
    <script defer src="/public/js/load-more-btn.js"></script>

    <title><?= htmlspecialchars($title ?? '') ?></title>
</head>
<body>
<?php require_once __DIR__ . '/___toast.php'; ?>

<!-- burger menu and container are part of the header/frame -->
<div id="container">
    <button class="burger" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
        <i class="fa-solid fa-xmark"></i>
    </button>

    <?php // include the sidenav component which renders the menu, notification flag and profile tag
    require_once __DIR__ . '/_nav.php'; ?>