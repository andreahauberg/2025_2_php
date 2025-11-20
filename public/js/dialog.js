document.addEventListener("DOMContentLoaded", () => {
  const openButtons = document.querySelectorAll("[data-open]");
  const closeButtons = document.querySelectorAll(
    ".x-dialog__close, .x-dialog__overlay"
  );

  // Open dialog
  openButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const target = btn.getAttribute("data-open");

      // Hvis det er update post-dialogboksen, indsæt postens data
      if (target === "updatePostDialog") {
        const postPk = btn.getAttribute("data-post-pk");
        const postElement = btn.closest(".post");
        const postMessage = postElement
          .querySelector(".text")
          .textContent.trim();

        document.getElementById("postPkInput").value = postPk;
        document.getElementById("postMessageInput").value = postMessage;
      }

      document.getElementById(target).classList.add("active");
    });
  });

  // Close dialog
  closeButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.closest(".x-dialog").classList.remove("active");
    });
  });

});
