// Åbn kommentar-dialog og hent kommentarer
document.querySelectorAll(".comment-btn").forEach((btn) => {
  btn.addEventListener("click", async function () {
    const postPk = this.getAttribute("data-post-pk");
    const userPk = this.getAttribute("data-user-pk");
    const dialog = document.getElementById(`commentDialog_${postPk}`);
    dialog.style.display = dialog.style.display === "none" ? "block" : "none";

    if (dialog.style.display === "block") {
      const response = await fetch(`/api-get-comments?post_pk=${postPk}`);
      const comments = await response.json();
      const container = document.getElementById(`commentsContainer_${postPk}`);
      container.innerHTML = comments
        .map(
          (comment) => `
                <div class="comment" data-comment-pk="${comment.comment_pk}">
                    <div class="comment-header">
                        <span class="name">${comment.user_full_name}</span>
                        
                        <span class="handle">
                            ${new Date(
                              comment.comment_created_at
                            ).toLocaleDateString("da-DK", {
                              day: "numeric",
                              month: "short",
                            })}
                            ${comment.updated_at ? " · Redigeret" : ""}
                        </span>
                        ${
                          comment.comment_user_fk === userPk
                            ? `<div class="comment-actions">
                                <button class="edit-comment-btn" data-comment-pk="${comment.comment_pk}">Edit</button>
                                <button class="delete-comment-btn" data-comment-pk="${comment.comment_pk}">Delete</button>
                            </div>`
                            : ""
                        }
                    </div>
                    <p class="comment-text">${comment.comment_message}</p>
                    <form class="edit-comment-form" style="display: none;" data-comment-pk="${
                      comment.comment_pk
                    }">
                        <textarea name="comment_message" class="edit-comment-textarea">${
                          comment.comment_message
                        }</textarea>
                        <button class="edit-comment-btn" type="submit">Save</button>
                        <button type="button" class="cancel-edit-btn">Cancel</button>
                    </form>
                </div>
            `
        )
        .join("");
    }
  });
});

// Rediger kommentar
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("edit-comment-btn")) {
    const commentPk = e.target.getAttribute("data-comment-pk");
    const commentDiv = document.querySelector(
      `.comment[data-comment-pk="${commentPk}"]`
    );
    commentDiv.querySelector(".comment-text").style.display = "none";
    commentDiv.querySelector(".edit-comment-form").style.display = "block";
  }
});

// Annuller redigering
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("cancel-edit-btn")) {
    const commentDiv = e.target.closest(".comment");
    commentDiv.querySelector(".comment-text").style.display = "block";
    commentDiv.querySelector(".edit-comment-form").style.display = "none";
  }
});

// Gem redigeret kommentar
document.addEventListener("submit", async function (e) {
  if (e.target.classList.contains("edit-comment-form")) {
    e.preventDefault();
    const form = e.target;
    const commentPk = form.getAttribute("data-comment-pk");
    const message = form.querySelector("textarea").value;
    const response = await fetch("/api-update-comment", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `comment_pk=${commentPk}&comment_message=${encodeURIComponent(
        message
      )}`,
    });
    if (response.ok) {
      const commentDiv = form.closest(".comment");
      commentDiv.querySelector(".comment-text").textContent = message;
      commentDiv.querySelector(".comment-text").style.display = "block";
      form.style.display = "none";
      const timeSpan = commentDiv.querySelector(".comment-time");
      if (!timeSpan.textContent.includes("Redigeret")) {
        timeSpan.textContent += " · Redigeret";
      }
    }
  }
});

// Slet kommentar
document.addEventListener("click", async function (e) {
  if (e.target.classList.contains("delete-comment-btn")) {
    if (confirm("Er du sikker på, at du vil slette denne kommentar?")) {
      const commentPk = e.target.getAttribute("data-comment-pk");
      const response = await fetch("/api-delete-comment", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `comment_pk=${commentPk}`,
      });
      if (response.ok) {
        const commentDiv = e.target.closest(".comment");
        const postPk = commentDiv
          .closest(".comments-container")
          .id.split("_")[1];
        commentDiv.remove();
        const btn = document.querySelector(
          `.comment-btn[data-post-pk="${postPk}"]`
        );
        const countSpan = btn.querySelector(".comment-count");
        countSpan.textContent = parseInt(countSpan.textContent) - 1;
      }
    }
  }
});

// Opret ny kommentar
document.addEventListener("submit", async function (e) {
  if (e.target.classList.contains("comment-form")) {
    e.preventDefault();
    const form = e.target;
    const postPk = form.getAttribute("data-post-pk");
    const message = form.querySelector("textarea").value;
    const response = await fetch("/api-create-comment", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `post_pk=${postPk}&comment_message=${encodeURIComponent(message)}`,
    });
    if (response.ok) {
      document.querySelector(`.comment-btn[data-post-pk="${postPk}"]`).click();
      form.reset();
      const countSpan = document.querySelector(
        `.comment-btn[data-post-pk="${postPk}"] .comment-count`
      );
      countSpan.textContent = parseInt(countSpan.textContent) + 1;
    }
  }
});
