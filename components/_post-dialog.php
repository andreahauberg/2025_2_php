<?php
// åben post dialog når server er sat til `$_SESSION['open_dialog'] = 'post'`
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$postActive = false;
if (!empty($_SESSION['open_dialog']) && $_SESSION['open_dialog'] === 'post') {
  $postActive = true;
  unset($_SESSION['open_dialog']);
}
?>

<div class="x-dialog <?php echo $postActive ? 'active' : ''; ?>" id="postDialog" role="dialog" aria-modal="true" aria-labelledby="postTitle">
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
    <h2 id="signupPost">Create your post</h2>
    <form class="x-dialog__form" action="api-create-post" method="POST" autocomplete="off">
        <textarea id="post-dialog-textarea" type="text" maxlength="300" name="post_message" placeholder="Your post message here"><?php echo isset($_SESSION['old_post_message']) ? htmlspecialchars($_SESSION['old_post_message']) : ''; ?></textarea>
      <button type="submit" class="x-dialog__btn">Post</button>
    </form>
  </div>
</div>