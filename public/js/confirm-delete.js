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
    } else {
      const ok = window.confirm("Are you sure you want to delete your profile? This action cannot be undone.");
      if (!ok) return;
    }

    window.location.href = "api-delete-profile";
  } catch (err) {
    // swallow errors silently
  }
});

// Intercept update-profile form submission to use fetch + toast
document.addEventListener("submit", async function (e) {
  const form = e.target.closest && e.target.closest("#updateProfileDialog .x-dialog__form");
  if (!form) return;
  e.preventDefault();
  //example how to get it to render the changes immediately
  try {
    const fd = new FormData(form);
    const res = await fetch(form.getAttribute("action") || "api-update-profile", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
      },
      body: fd,
    });

    let data = null;
    try {
      data = await res.json();
    } catch (e) {
      showToast("Could not update profile", "error");
      return;
    }

    if (!res.ok || !data || data.success !== true) {
      // Specific field errors
      if (data && data.error_code === "no_change") {
        showToast(data.message || "Please change something before updating", "error");
        return;
      }

      if (data && data.error_code === "email_taken") {
        showToast(data.message || "Email is already taken", "error");
        const emailInput = form.querySelector('input[name="user_email"]');
        if (emailInput) emailInput.focus();
        return;
      }

      if (data && data.error_code === "username_taken") {
        showToast(data.message || "Username is already taken", "error");
        const usernameInput = form.querySelector('input[name="user_username"]');
        if (usernameInput) usernameInput.focus();
        return;
      }

      showToast((data && (data.message || data.error)) || "Could not update profile", "error");
      return;
    }

    showToast(data.message || "Profile updated", "ok");
    // reload so feed/nav/trending renders with new username
    setTimeout(() => window.location.reload(), 600);
    return;
  } catch (err) {
    showToast("Network error while updating profile", "error");
  }
});
