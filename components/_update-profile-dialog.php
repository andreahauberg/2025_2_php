<div class="x-dialog" id="updateProfileDialog" role="dialog" aria-modal="true" aria-labelledby="updateProfileTitle">
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
    <h2 id="updateProfileTitle">Update your information</h2>
    <form class="x-dialog__form" action="api-update-profile" method="POST" autocomplete="off">
      <input
        name="user_full_name"
        type="text"
        maxlength="20"
        placeholder="Name"
        value="<?php _($_SESSION["user"]["user_full_name"] ?? ''); ?>"
        required
      >
      <input
        name="user_username"
        type="text"
        maxlength="20"
        placeholder="Username"
        value="<?php _($_SESSION["user"]["user_username"] ?? ''); ?>"
        required
      >
      <input
        name="user_email"
        type="email"
        maxlength="50"
        placeholder="Email"
        value="<?php _($_SESSION["user"]["user_email"] ?? ''); ?>"
        required
      >
      <button type="submit" class="x-dialog__btn">Update</button>
      <button type="button" class="x-dialog__btn_del">Delete</button>
    </form>
  </div>
</div>
<!-- <script>
  document.querySelector('.x-dialog__btn_del').addEventListener('click', function() {
    if (confirm('Are you sure you want to delete your profile? This action cannot be undone.')) {
      window.location.href = 'api-delete-profile';
    }
  });
</script> -->