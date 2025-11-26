// public/js/delete-confirm.js
function initDeleteConfirm() {
  const dialog = document.getElementById("updatePostDialog");
  if (!dialog) return;

  const deleteBtn = dialog.querySelector("#deletePostBtn");
  const confirmBox = dialog.querySelector("#deleteConfirm");
  const cancelBtn = dialog.querySelector("#deleteCancel");
  const confirmYes = dialog.querySelector("#deleteConfirmYes");
  const postPkInput = dialog.querySelector("#postPkInput");

  if (!deleteBtn || !confirmBox || !cancelBtn || !confirmYes || !postPkInput) {
    return;
  }

  deleteBtn.addEventListener("click", () => {
    confirmBox.classList.add("delete-confirm--visible");
  });

  cancelBtn.addEventListener("click", () => {
    confirmBox.classList.remove("delete-confirm--visible");
  });

  // ⬇⬇⬇ ændr DENNE del ⬇⬇⬇
  confirmYes.addEventListener("click", () => {
    const pk = postPkInput.value;
    if (!pk) return;

    // lad browseren navigere – inklusiv redirect tilbage til nuværende side
    const redirect = window.location.pathname + window.location.search;
    window.location.href = "api-delete-post?post_pk=" + encodeURIComponent(pk) + "&redirect_to=" + encodeURIComponent(redirect);
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initDeleteConfirm);
} else {
  initDeleteConfirm();
}
