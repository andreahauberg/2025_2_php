// Åbn kommentar-dialog
document.querySelectorAll(".comment-btn").forEach((btn) => {
    btn.addEventListener("click", async function () {
        const postPk = this.getAttribute("data-post-pk");
        const dialog = document.getElementById(`commentDialog_${postPk}`);
        dialog.style.display = dialog.style.display === "none" ? "block" : "none";

        if (dialog.style.display === "block") {
            // Hent kommentarer
            const response = await fetch(`/api-get-comments?post_pk=${postPk}`);
            const comments = await response.json();
            const container = document.getElementById(`commentsContainer_${postPk}`);
            container.innerHTML = comments
                .map(
                    (comment) => `
                <div class="comment">
                <div class="comment-header">
                    <span class="name">${comment.user_full_name}</span>
                    <span class="handle">@${comment.user_username} · </span>
                    <span class="handle">
                        ${new Date(comment.comment_created_at).toLocaleDateString("da-DK", {
                        day: "numeric",
                        month: "short",
                        })}
                    </span>
                </div>
                <p class="comment-text">${comment.comment_message}</p>
            </div>
            `
                )
                .join("");
        }
    });
});

// Håndter indsendelse af kommentar
// Håndter indsendelse af kommentar
document.querySelectorAll(".comment-form").forEach((form) => {
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        const postPk = this.getAttribute("data-post-pk");
        const message = this.querySelector("textarea").value;
        const response = await fetch("/api-create-comment", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `post_pk=${postPk}&comment_message=${encodeURIComponent(message)}`,
        });
        if (response.ok) {
            // Genindlæs kommentarer
            const btn = document.querySelector(
                `.comment-btn[data-post-pk="${postPk}"]`
            );
            btn.click(); // Åbn dialogboksen igen for at vise den nye kommentar

            // Opdater kommentar-tælleren
            const commentCountSpan = btn.querySelector(".comment-count");
            const currentCount = parseInt(commentCountSpan.textContent);
            commentCountSpan.textContent = currentCount + 1;

            this.reset(); // Nulstil formularen
        }
    });
});
