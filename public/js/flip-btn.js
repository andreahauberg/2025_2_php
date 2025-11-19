export function flipBtn() {
  document.querySelectorAll(".flip-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const heartIcon = this.querySelector("i");
      const postPk = this.getAttribute("data-post-pk");
      const likeCountSpan = this.querySelector(".like-count");
      const isLiked = heartIcon.classList.contains("fa-solid");

      // Send en anmodning til serveren
      fetch(
        isLiked
          ? `api-unlike-post?post_pk=${postPk}`
          : `api-like-post?post_pk=${postPk}`,
        {
          method: "GET",
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.text();
        })
        .then((data) => {
          // Opdater UI
          if (isLiked) {
            heartIcon.classList.remove("fa-solid");
            heartIcon.classList.add("fa-regular");
            likeCountSpan.textContent = parseInt(likeCountSpan.textContent) - 1;
          } else {
            heartIcon.classList.remove("fa-regular");
            heartIcon.classList.add("fa-solid");
            likeCountSpan.textContent = parseInt(likeCountSpan.textContent) + 1;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Something went wrong. Please try again.");
          // Flip tilbage, hvis der er en fejl
          if (isLiked) {
            heartIcon.classList.remove("fa-regular");
            heartIcon.classList.add("fa-solid");
          } else {
            heartIcon.classList.remove("fa-solid");
            heartIcon.classList.add("fa-regular");
          }
        });
    });
  });
}
