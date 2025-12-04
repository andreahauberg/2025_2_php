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
    // hide the original delete button while the confirmation is visible
    deleteBtn.style.display = "none";
  });

  cancelBtn.addEventListener("click", () => {
    confirmBox.classList.remove("delete-confirm--visible");
    // restore the delete button when cancelling
    deleteBtn.style.display = "";
  });

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

// Delete user
document.addEventListener("click", async function (e) {
  try {
    const btn = e.target.closest && e.target.closest(".x-dialog__btn_del");
    if (!btn) return;
    const dialog = btn.closest("#updateProfileDialog");
    if (!dialog) return;

    e.preventDefault();
    const container = dialog.querySelector(".x-dialog__form") || dialog;

    if (typeof showDeleteConfirmInline === "function") {
      const ok = await showDeleteConfirmInline(container, "Are you sure you want to delete your profile? This action cannot be undone.");
      if (!ok) return;
      window.location.href = "api-delete-profile";
    } else {
      const ok = window.confirm("Are you sure you want to delete your profile? This action cannot be undone.");
      if (!ok) return;
      window.location.href = "api-delete-profile";
    }
  } catch (err) {
    console.error("[confirm-delete] error handling profile delete", err);
  }
});
