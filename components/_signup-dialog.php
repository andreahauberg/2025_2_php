<?php
// åben når server er sat til `$_SESSION['open_dialog'] = 'signup'`
$__signup_active = '';
if (!empty($_SESSION['open_dialog']) && $_SESSION['open_dialog'] === 'signup') {
  $__signup_active = ' active';
  unset($_SESSION['open_dialog']);
}
?>
<div class="x-dialog<?php echo $__signup_active; ?>" id="signupDialog" role="dialog" aria-modal="true" aria-labelledby="signupTitle">
  <div class="x-dialog__overlay"></div>
  <div class="x-dialog__content">
    <button class="x-dialog__close" aria-label="Close">&times;</button>
    <div class="x-dialog__header">
    <img src="/public/img/weave-logo.png" alt="Weave logo" class="post-logo">
    </div>
    <h2 id="signupTitle">Create your account</h2>
    <form class="x-dialog__form" action="bridge-signup" method="POST" autocomplete="off">
      <input name="user_full_name" type="text" maxlength="20" placeholder="Name" required <?php if ($__signup_active) echo 'autofocus'; ?>>
      <input name="user_username" type="text" maxlength="20" placeholder="Username" required>
      <input name="user_email" type="email" maxlength="50" placeholder="Email" required>
      <input name="user_password" type="password" maxlength="50" placeholder="Password" required>
      <button type="submit" class="x-dialog__btn">Sign up</button>
    </form>
    <p class="x-dialog__alt">Already have an account? <a href="#" data-open="loginDialog">Log in</a></p>
  </div>
</div>