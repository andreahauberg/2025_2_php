<?php 
    session_start();
    $redirectUrl = isset($_SESSION['user']) ? '/home' : '/';
    ?>
<?php 
$title = "404";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" type="image/x-icon" href="/public/favicon/favicon.ico">
  <link rel="stylesheet" href="/public/css/app.css">
  <link rel="stylesheet" href="/public/css/search.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script type="module" src="/public/js/app.js"></script>
  <script defer src="/public/js/dialog.js"></script>
</head>
<body>

<?php require_once __DIR__ . '/../components/___toast.php'; ?>

<main class="x-landing">
  <div class="x-landing__left">
      <div class="x-landing__logo" aria-hidden="true">
      <img src="/public/img/weave-logo.png" alt="Weave logo" class="logo">
      </div>
  </div>

  <div class="x-landing__right">
    <h1 class="x-landing__title">404</h1>
    <h2 class="x-landing__subtitle">OOOOOOOOOPS AN ERROR OCCOURED</h2>

    <a class="x-landing__btn x-landing__btn--signup" href="<?= htmlspecialchars($redirectUrl) ?>">go back</a>
  </div>


<?php 


?>

</main>

<?php require_once __DIR__."/../components/_footer.php"; ?>