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
      <svg class="x-dialog__logo" viewBox="0 0 300 300">
        <g fill="none" stroke="#0b0f11" stroke-width="44">
          <line x1="40"  y1="40"  x2="260" y2="260"/>
          <line x1="260" y1="40"  x2="40"  y2="260"/>
        </g>
      </svg>
    </div>
    <h2 id="signupTitle">Create your account</h2>
    <form class="x-dialog__form" action="bridge-signup" method="POST" autocomplete="off">
      <input name="user_full_name" type="text" placeholder="Name" required <?php if ($__signup_active) echo 'autofocus'; ?>>
      <input name="user_username" type="text" placeholder="Username" required>
      <input name="user_email" type="email" placeholder="Email" required>
      <input name="user_password" type="password" placeholder="Password" required>
      <button type="submit" class="x-dialog__btn">Sign up</button>
    </form>
    <p class="x-dialog__alt">Already have an account? <a href="#" data-open="loginDialog">Log in</a></p>
  </div>
</div>