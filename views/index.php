<?php 
$title = "Welcome";
require_once __DIR__."/../components/_header.php";
?>


<main class="x-landing">
  <div class="x-landing__left">
      <div class="x-landing__logo" aria-hidden="true">
      <img src="/public/img/weave-logo.png" alt="Weave logo" class="logo">
      </div>
  </div>

  <div class="x-landing__right">
    <h1 class="x-landing__title">Happening now</h1>
    <h2 class="x-landing__subtitle">Join today.</h2>

    <button class="x-landing__btn x-landing__btn--signup" data-open="signupDialog">Sign up</button>
    <button class="x-landing__btn x-landing__btn--login" data-open="loginDialog">Log in</button>
  </div>


<?php 
require_once __DIR__."/../db.php";
require_once __DIR__."/../components/_login-dialog.php"; 
require_once __DIR__."/../components/_signup-dialog.php"; 

?>

</main>

<?php require_once __DIR__."/../components/_footer.php"; ?>