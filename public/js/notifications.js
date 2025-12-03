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

  // init when DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      update();
      setInterval(update, POLL_MS);
    });
  } else {
    update();
    setInterval(update, POLL_MS);
  }
})();
