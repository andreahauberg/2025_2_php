// ---------------- TOAST ----------------
function showToast(message, type = "ok", ttl = 5000) {
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }

  const toast = document.createElement("div");
  toast.className = "toast " + (type === "error" ? "toast-error" : "toast-ok");
  toast.textContent = message;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 500);
  }, ttl);
}

// ------------- INLINE DELETE CONFIRM -------------
function showDeleteConfirmInline(containerElement, message) {
  return new Promise((resolve) => {
    if (!containerElement) {
      resolve(false);
      return;
    }

    // fjern evt. gammel confirm
    const existing = containerElement.querySelector(".delete-confirm");
    if (existing) existing.remove();

    const dialog = document.createElement("div");
    dialog.className = "delete-confirm delete-confirm--visible";
    dialog.innerHTML = `
      <span class="delete-confirm__message"></span>
      <div class="delete-confirm__buttons">
        <button class="delete-confirm__btn delete-confirm__btn--secondary">Annuller</button>
        <button class="delete-confirm__btn delete-confirm__btn--danger">OK</button>
      </div>
    `;

    const msgEl = dialog.querySelector(".delete-confirm__message");
    const btnCancel = dialog.querySelector(".delete-confirm__btn--secondary");
    const btnOk = dialog.querySelector(".delete-confirm__btn--danger");
    msgEl.textContent = message;

    function cleanup(result) {
      btnCancel.removeEventListener("click", onCancel);
      btnOk.removeEventListener("click", onOk);
      dialog.remove();
      resolve(result);
    }

    function onCancel(e) {
      e.preventDefault();
      cleanup(false);
    }

    function onOk(e) {
      e.preventDefault();
      cleanup(true);
    }

    btnCancel.addEventListener("click", onCancel);
    btnOk.addEventListener("click", onOk);

    containerElement.appendChild(dialog);
    dialog.scrollIntoView({ behavior: "smooth", block: "end" });
  });
}

// ------------- FÆLLES POST-HJÆLPER -------------
async function sendForm(url, payload) {
  const body = new URLSearchParams(payload).toString();

  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  });

  let data = {};
  try {
    data = await response.json();
  } catch (_) {
    // hvis serveren ikke sender valid JSON er data bare {}
  }

  return { response, data };
}

// ------------- HENT & RENDER KOMMENTARER -------------
async function loadComments(postPk, userPk) {
  const res = await fetch(`/api-get-comments?post_pk=${encodeURIComponent(postPk)}`);
  if (!res.ok) return;

  const comments = await res.json();
  const container = document.getElementById(`commentsContainer_${postPk}`);
  if (!container) return;

  container.innerHTML = "";
  comments.forEach((comment) => {
    const node = createCommentElement(comment, userPk);
    container.appendChild(node);
  });
}

// helper: build a comment DOM node from a comment object
function createCommentElement(comment, userPk) {
  const commentDiv = document.createElement("div");
  commentDiv.className = "comment";
  commentDiv.dataset.commentPk = comment.comment_pk;

  const header = document.createElement("div");
  header.className = "comment-header";

  const name = document.createElement("span");
  name.className = "name";
  name.textContent = comment.user_full_name || "";

  const handle = document.createElement("span");
  handle.className = "handle";
  const dateStr = comment.comment_created_at ? new Date(comment.comment_created_at).toLocaleDateString("da-DK", { day: "numeric", month: "short" }) : "";
  handle.textContent = dateStr + (comment.updated_at ? " · Redigeret" : "");

  header.appendChild(name);
  header.appendChild(handle);

  if (comment.comment_user_fk == userPk) {
    const actions = document.createElement("div");
    actions.className = "comment-actions";

    const editBtn = document.createElement("button");
    editBtn.className = "edit-comment-btn";
    editBtn.dataset.commentPk = comment.comment_pk;
    editBtn.textContent = "Edit";

    const delBtn = document.createElement("button");
    delBtn.className = "delete-comment-btn";
    delBtn.dataset.commentPk = comment.comment_pk;
    delBtn.textContent = "Delete";

    actions.appendChild(editBtn);
    actions.appendChild(delBtn);
    header.appendChild(actions);
  }

  const textP = document.createElement("p");
  textP.className = "comment-text";
  textP.textContent = comment.comment_message || "";

  const form = document.createElement("form");
  form.className = "edit-comment-form";
  form.dataset.commentPk = comment.comment_pk;
  form.style.display = "none";

  const ta = document.createElement("textarea");
  ta.name = "comment_message";
  ta.className = "edit-comment-textarea";
  ta.value = comment.comment_message || "";

  const saveBtn = document.createElement("button");
  saveBtn.type = "submit";
  saveBtn.className = "edit-comment-btn";
  saveBtn.textContent = "Save";

  const cancelBtn = document.createElement("button");
  cancelBtn.type = "button";
  cancelBtn.className = "cancel-edit-btn";
  cancelBtn.textContent = "Cancel";

  form.appendChild(ta);
  form.appendChild(saveBtn);
  form.appendChild(cancelBtn);

  commentDiv.appendChild(header);
  commentDiv.appendChild(textP);
  commentDiv.appendChild(form);

  return commentDiv;
}

// ------------- ÅBN/LUK KOMMENTAR-DIALOG -------------
document.querySelectorAll(".comment-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    const postPk = btn.getAttribute("data-post-pk");
    const userPk = btn.getAttribute("data-user-pk");
    const dialog = document.getElementById(`commentDialog_${postPk}`);
    if (!dialog) return;

    dialog.style.display = dialog.style.display === "none" ? "block" : "none";

    if (dialog.style.display === "block") {
      loadComments(postPk, userPk).catch(console.error);
    }
  });
});

// ------------- EDIT VIS/ANNULLER -------------
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("edit-comment-btn") && e.target.closest(".comment")) {
    const commentPk = e.target.getAttribute("data-comment-pk");
    const commentDiv = document.querySelector(`.comment[data-comment-pk="${commentPk}"]`);
    if (!commentDiv) return;
    commentDiv.querySelector(".comment-text").style.display = "none";
    commentDiv.querySelector(".edit-comment-form").style.display = "block";
  }

  if (e.target.classList.contains("cancel-edit-btn")) {
    const commentDiv = e.target.closest(".comment");
    if (!commentDiv) return;
    commentDiv.querySelector(".comment-text").style.display = "block";
    commentDiv.querySelector(".edit-comment-form").style.display = "none";
  }
});

// ------------- GEM REDIGERET KOMMENTAR -------------
document.addEventListener("submit", async (e) => {
  if (!e.target.classList.contains("edit-comment-form")) return;
  e.preventDefault();

  const form = e.target;
  const commentPk = form.dataset.commentPk;
  const message = form.querySelector("textarea").value;

  try {
    const { response, data } = await sendForm("/api-update-comment", {
      comment_pk: commentPk,
      comment_message: message,
    });

    if (!response.ok || data.success === false) {
      const msg = (data && (data.error || data.message)) || "Noget gik galt ved opdatering af kommentaren";
      showToast(msg, "error");
      return;
    }

    const commentDiv = form.closest(".comment");
    if (!commentDiv) return;

    const textEl = commentDiv.querySelector(".comment-text");
    textEl.textContent = message;
    textEl.style.display = "block";
    form.style.display = "none";

    const handleEl = commentDiv.querySelector(".handle");
    if (handleEl && !handleEl.textContent.includes("Redigeret")) {
      handleEl.textContent = handleEl.textContent.trim() + " · Redigeret";
    }

    showToast((data && data.message) || "Kommentar opdateret", "ok");
  } catch (err) {
    console.error("Error updating comment:", err);
    showToast("Noget gik galt ved opdatering af kommentaren", "error");
  }
});

// ------------- SLET KOMMENTAR -------------
document.addEventListener("click", async (e) => {
  if (!e.target.classList.contains("delete-comment-btn")) return;

  const commentDiv = e.target.closest(".comment");
  if (!commentDiv) return;

  e.preventDefault();
  e.stopPropagation();

  const ok = await showDeleteConfirmInline(commentDiv, "Er du sikker på, at du vil slette denne kommentar?");
  if (!ok) return;

  const commentPk = commentDiv.dataset.commentPk;

  try {
    const { response, data } = await sendForm("/api-delete-comment", {
      comment_pk: commentPk,
    });

    if (!response.ok || data.success === false) {
      const msg = (data && (data.error || data.message)) || "Noget gik galt ved sletning af kommentaren";
      showToast(msg, "error");
      return;
    }

    const postPk = commentDiv.closest(".comments-container").id.split("_")[1];
    commentDiv.remove();

    const btn = document.querySelector(`.comment-btn[data-post-pk="${postPk}"]`);
    if (btn) {
      const countSpan = btn.querySelector(".comment-count");
      if (countSpan) {
        countSpan.textContent = parseInt(countSpan.textContent, 10) - 1;
      }
    }

    showToast((data && data.message) || "Kommentar slettet", "ok");
  } catch (err) {
    console.error("Error deleting comment:", err);
    showToast("Noget gik galt ved sletning af kommentaren", "error");
  }
});

// ------------- OPRET NY KOMMENTAR -------------

document.addEventListener("submit", async (e) => {
  if (!e.target.classList.contains("comment-form")) return;
  e.preventDefault();

  const form = e.target;
  const postPk = form.getAttribute("data-post-pk");
  const message = form.querySelector("textarea").value;

  try {
    const { response, data } = await sendForm("/api-create-comment", {
      post_pk: postPk,
      comment_message: message,
    });

    // Kun succes hvis success === true
    if (!response.ok || !data || data.success !== true) {
      const msg = (data && (data.error || data.message)) || "Noget gik galt ved oprettelse";
      showToast(msg, "error");
      return;
    }

    // Succes-toast
    showToast(data.message || "Kommentar oprettet", "ok");

    // Reload comments
    const commentBtn = document.querySelector(`.comment-btn[data-post-pk="${postPk}"]`);
    const userPk = commentBtn ? commentBtn.getAttribute("data-user-pk") : null;
    await loadComments(postPk, userPk);

    form.reset();
  } catch (err) {
    console.error("Error creating comment:", err);
    showToast("Noget gik galt ved oprettelse", "error");
  }
});
