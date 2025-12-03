(function () {
  const API = "/api/api-get-unread-notif-count.php";
  const POLL_MS = 30000; // 30s

  function findBellAnchors() {
    // find nav anchors that have a bell icon
    return Array.from(document.querySelectorAll("nav a")).filter((a) => {
      return a.querySelector("i.fa-bell") || a.querySelector("i.fa-bell-o") || a.querySelector("i.fa-regular.fa-bell") || a.querySelector("i.fa-solid.fa-bell");
    });
  }

  function ensureflag(a) {
    a.classList.add("notif-link");
    let flag = a.querySelector(".notif-flag");
    if (!flag) {
      flag = document.createElement("span");
      flag.className = "notif-flag";
      flag.setAttribute("aria-hidden", "true");
      a.appendChild(flag);
    }
    return flag;
  }

  async function fetchCount() {
    try {
      const res = await fetch(API, { credentials: "same-origin" });
      if (!res.ok) return null;
      const data = await res.json();
      if (!data || data.success !== true) return 0;
      return Number(data.unread_count) || 0;
    } catch (e) {
      console.error("fetch unread count", e);
      return null;
    }
  }

  async function update() {
    const anchors = findBellAnchors();
    if (anchors.length === 0) return;
    const count = await fetchCount();
    if (count === null) return; // network error - keep existing
    anchors.forEach((a) => {
      const flag = ensureflag(a);
      if (count > 0) {
        flag.textContent = count > 99 ? "99+" : String(count);
        flag.classList.add("notif-flag--visible");
      } else {
        flag.textContent = "";
        flag.classList.remove("notif-flag--visible");
      }
    });
  }

  // mark single notification as read via API and update UI
  async function markNotification(pk) {
    try {
      const res = await fetch("/api/api-mark-notification-read.php", {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `notification_pk=${encodeURIComponent(pk)}`,
      });
      if (!res.ok) return false;
      const text = await res.text();
      try {
        const data = JSON.parse(text);
        return data.success === true;
      } catch (e) {
        console.error("markNotification non-json response", text);
        return false;
      }
    } catch (e) {
      console.error("markNotification", e);
      return false;
    }
  }

  // mark all notifications as read
  async function markAll() {
    try {
      const res = await fetch("/api/api-mark-all-notifications-read.php", {
        method: "POST",
        credentials: "same-origin",
      });
      if (!res.ok) return false;
      const text = await res.text();
      try {
        const data = JSON.parse(text);
        return data.success === true;
      } catch (e) {
        console.error("markAll non-json response", text);
        return false;
      }
    } catch (e) {
      console.error("markAll", e);
      return false;
    }
  }

  // UI helpers for notifications page
  function handleMarkClick(e) {
    const btn = e.target.closest(".notif-mark-btn");
    if (!btn) return;
    const pk = btn.getAttribute("data-notif-pk");
    if (!pk) return;
    btn.disabled = true;
    markNotification(pk).then((ok) => {
      btn.disabled = false;
      if (ok) {
        const row = document.querySelector(`.post[data-notif-pk="${pk}"]`);
        if (row) {
          row.classList.remove("notification--unread");
        }
        // decrement badge count by 1
        updateBadgeDelta(-1);
      } else {
        console.warn("Failed to mark notification");
      }
    });
  }

  function handleMarkAllClick() {
    const btn = document.getElementById("markAllBtn");
    if (!btn) return;
    btn.disabled = true;
    markAll().then((ok) => {
      btn.disabled = false;
      if (ok) {
        document.querySelectorAll(".post.notification--unread").forEach((row) => row.classList.remove("notification--unread"));
        // refresh badge (set to 0)
        setBadgeCount(0);
      }
    });
  }

  function currentBadgeNode() {
    const anchors = findBellAnchors();
    return anchors.length ? ensureflag(anchors[0]) : null;
  }

  function setBadgeCount(n) {
    const node = currentBadgeNode();
    if (!node) return;
    if (n > 0) {
      node.textContent = n > 99 ? "99+" : String(n);
      node.classList.add("notif-flag--visible");
    } else {
      node.textContent = "";
      node.classList.remove("notif-flag--visible");
    }
  }

  // adjust current badge by delta (can be negative)
  function updateBadgeDelta(delta) {
    const node = currentBadgeNode();
    if (!node) return;
    const cur = node.textContent ? (node.textContent === "99+" ? 100 : Number(node.textContent)) : 0;
    let next = cur + delta;
    if (next < 0) next = 0;
    if (next === 0) {
      node.textContent = "";
      node.classList.remove("notif-flag--visible");
    } else {
      node.textContent = next > 99 ? "99+" : String(next);
      node.classList.add("notif-flag--visible");
    }
  }

  // wire events on notifications page
  function initNotificationsPage() {
    document.addEventListener("click", function (e) {
      if (e.target.closest(".notif-mark-btn")) {
        handleMarkClick(e);
      }
      if (e.target.closest(".notif-delete-btn")) {
        handleDeleteClick(e);
      }
      if (e.target.id === "markAllBtn") {
        handleMarkAllClick();
      }
    });
  }

  // delete single notification via API and update UI
  async function deleteNotification(pk) {
    try {
      const res = await fetch("/api/api-delete-notification.php", {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `notification_pk=${encodeURIComponent(pk)}`,
      });
      if (!res.ok) return false;
      const text = await res.text();
      try {
        const data = JSON.parse(text);
        return data.success === true;
      } catch (e) {
        console.error("deleteNotification non-json response", text);
        return false;
      }
    } catch (e) {
      console.error("deleteNotification", e);
      return false;
    }
  }

  function handleDeleteClick(e) {
    const btn = e.target.closest(".notif-delete-btn");
    if (!btn) return;
    const pk = btn.getAttribute("data-notif-pk");
    if (!pk) return;
    const row = document.querySelector(`.post[data-notif-pk="${pk}"]`);
    const wasUnread = row && row.classList.contains("notification--unread");
    btn.disabled = true;
    deleteNotification(pk).then((ok) => {
      btn.disabled = false;
      if (ok) {
        if (row) row.remove();
        if (wasUnread) updateBadgeDelta(-1);
      } else {
        console.warn("Failed to delete notification");
      }
    });
  }

  // init when DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      update();
      setInterval(update, POLL_MS);
      initNotificationsPage();
    });
  } else {
    update();
    setInterval(update, POLL_MS);
    initNotificationsPage();
  }
})();
