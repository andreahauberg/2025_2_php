<?php
// åben når server er sat til `$_SESSION['open_dialog'] = 'login'`
$__login_active = '';
if (!empty($_SESSION['open_dialog']) && $_SESSION['open_dialog'] === 'login') {
    $__login_active = ' active';
    unset($_SESSION['open_dialog']);
}
?>
<div class="x-dialog<?php echo $__login_active; ?>" id="loginDialog" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
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
    <h2 id="loginTitle">Log in to X</h2>
    <form class="x-dialog__form" action="bridge-login" method="POST" autocomplete="off">
      <input name="user_email" type="text" placeholder="Email" required <?php if ($__login_active) echo 'autofocus'; ?> >
      <input name="user_password" type="password" placeholder="Password" required>
      <button class="x-dialog__btn">Next</button>
    </form>
    <p class="x-dialog__alt">Don't have an account? <a href="#" data-open="signupDialog">Sign up</a></p>
  </div>
</div>