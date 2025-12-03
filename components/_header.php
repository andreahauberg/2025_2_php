<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../public/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="../public/js/dialog.js"></script>
    <script defer src="../public/js/notifications.js"></script>
    <title>
        <?php echo $title ?>
    </title>
</head>
<body>
<?php
require_once __DIR__ . '/___toast.php';
?>



<!-- 
<nav>
    <a href="/">Home</a>
    <a href="/contact-us">Contact</a>
</nav>
     -->