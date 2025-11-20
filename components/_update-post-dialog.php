<div class="x-dialog" id="updatePostDialog" role="dialog" aria-modal="true" aria-labelledby="updatePostTitle">
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
    <h2 id="updatePostTitle">Edit your post</h2>
    <form class="x-dialog__form" action="api-update-post" method="POST" autocomplete="off">
        <input type="hidden" name="post_pk" id="postPkInput">
        <textarea type="text" maxlength="300" name="post_message" id="postMessageInput" placeholder="Your post message here" required></textarea>
      <button type="submit" class="x-dialog__btn">Update</button>
      <button type="button" class="x-dialog__btn_del" id="deletePostBtn">Delete</button>
    </form>
  </div>
</div>

<script>
document.getElementById("deletePostBtn").addEventListener("click", function() {
    const postPk = document.getElementById("postPkInput").value;
    if (confirm("Are you sure you want to delete this post? This action cannot be undone.")) {
        window.location.href = `api-delete-post?post_pk=${postPk}`;
    }
});
</script>

