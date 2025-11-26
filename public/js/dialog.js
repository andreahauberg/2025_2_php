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

        if (pkInput) pkInput.value = postPk || "";
        if (msgInput) msgInput.value = postMessage || "";
        // redirect_to lader vi PHP styre (hidden input med $_SERVER['REQUEST_URI'])
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
