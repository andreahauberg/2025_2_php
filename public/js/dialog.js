document.addEventListener("DOMContentLoaded", () => {
  const openButtons = document.querySelectorAll("[data-open]");
  const closeButtons = document.querySelectorAll(".x-dialog__close, .x-dialog__overlay");

  // Open dialog
  openButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const targetId = btn.getAttribute("data-open");
      const dialog = document.getElementById(targetId);
      if (!dialog) return;

      if (targetId === "updatePostDialog") {
        const postPk = btn.getAttribute("data-post-pk");
        let postMessage = "";
        const postElement = btn.closest(".post");
        if (postElement) {
          const m = postElement.querySelector(".text");
          postMessage = m ? m.textContent.trim() : "";
        }

        const pkInput = dialog.querySelector("#postPkInput");
        const msgInput = dialog.querySelector("#postMessageInput");
        const redirectInput = dialog.querySelector("#redirectToInput");
        if (pkInput) pkInput.value = postPk || "";
        if (msgInput) msgInput.value = postMessage || "";
        // ensure redirect_to is set so server can redirect back to this page
        const redirectValue = window.location.pathname + window.location.search;
        if (redirectInput) redirectInput.value = redirectValue;
        // also set form action query as a fallback (in case hidden input doesn't submit)
        const form = dialog.querySelector("form");
        if (form) {
          const base = (form.getAttribute("action") || "api-update-post").split("?")[0];
          form.setAttribute("action", base + "?redirect_to=" + encodeURIComponent(redirectValue));
        }
      }

      dialog.classList.add("active");
    });
  });

  // Close dialog
  closeButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const dialog = btn.closest(".x-dialog");
      if (dialog) dialog.classList.remove("active");
    });
  });
});
