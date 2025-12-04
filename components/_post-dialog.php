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
    <img src="/public/img/weave-logo.png" alt="Weave logo" class="post-logo">
    <h2 id="signupPost">Create your post</h2>
    <form class="x-dialog__form" action="api-create-post" method="POST" enctype="multipart/form-data" autocomplete="off">
      <input
        type="hidden"
        name="redirect_to"
        id="redirectToInput"
        value="<?php _($_SERVER['REQUEST_URI']); ?>">
      <input
        type="hidden"
        name="post_pk"
        id="postPkInput"
        value="<?php _($updatePk); ?>">
        <textarea id="post-dialog-textarea" type="text" maxlength="200" name="post_message" placeholder="Your post message here"><?php echo isset($_SESSION['old_post_message']) ? htmlspecialchars($_SESSION['old_post_message']) : ''; ?></textarea>
        <input 
  type="file" 
  name="post_image" 
  accept="image/*"
  class="x-file-input"
/>
      <button type="submit" class="x-dialog__btn">Post</button>
    </form>
  </div>
</div>