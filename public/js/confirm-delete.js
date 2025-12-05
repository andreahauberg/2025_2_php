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

  try {
    const data = new URLSearchParams(new FormData(form)).toString();
    const res = await fetch(form.getAttribute("action") || "api-update-profile", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: data,
    });

    let json = {};
    let contentType = res.headers.get("Content-Type") || res.headers.get("content-type") || "";
    try {
      json = await res.json();
    } catch (_) {
      json = {};
    }

    if (res.ok && json && json.success === true) {
      if (typeof showToast === "function") showToast(json.message || "Profile updated", "ok");
      // update session values in UI where possible (page reload is safest)
      setTimeout(() => window.location.reload(), 800);
      return;
    }

    // handle no-change
    if (json && json.error_code === "no_change") {
      if (typeof showToast === "function") showToast(json.message || "Please change something before updating", "error");
      return;
    }

    const msg = (json && (json.message || json.error)) || "Could not update profile";
    if (typeof showToast === "function") showToast(msg, "error");
  } catch (err) {
    if (typeof showToast === "function") showToast("Network error while updating profile", "error");
  }
});
