// Shared toast helper used across multiple scripts
function showToast(message, type = "ok", ttl = 5000) {
  // try to reuse server-rendered container if present (___toast.php uses id="toast")
  let container = document.getElementById("toast") || document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    container.id = "toast";
    document.body.appendChild(container);
  }

  const toast = document.createElement("div");
  toast.className = "toast " + (type === "error" ? "toast-error" : "toast-ok");
  toast.dataset.ttl = String(ttl);
  toast.textContent = message;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 500);
  }, ttl);
}

// If server rendered a toast via components/___toast.php, animate & remove it
document.addEventListener("DOMContentLoaded", () => {
  const serverToast = document.querySelector("#toast .toast, .toast-container .toast");
  if (serverToast) {
    const ttlAttr = serverToast.dataset.ttl;
    const ttl = ttlAttr ? parseInt(ttlAttr, 10) : 5000;

    setTimeout(() => {
      serverToast.style.opacity = "0";
      setTimeout(() => {
        const container = serverToast.closest(".toast-container") || document.getElementById("toast");
        if (serverToast.parentElement) serverToast.remove();
        if (container && container.children.length === 0) container.remove();
      }, 500);
    }, ttl);
  }
});

// expose on window explicitly
window.showToast = showToast;
