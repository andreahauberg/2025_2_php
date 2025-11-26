document.addEventListener("DOMContentLoaded", () => {
  function setupLoadMore({ buttonId, listId, url, defaultLimit, handleNonOk, renderItem }) {
    const btn = document.getElementById(buttonId);
    const list = document.getElementById(listId);
    if (!btn || !list) return;

    const initialCount = parseInt(btn.dataset.initial || list.children.length || "0", 10);
    const maxItems = parseInt(btn.dataset.max || "10", 10);

    // default state
    btn.dataset.mode = btn.dataset.mode || "more";
    if (!btn.dataset.offset) {
      btn.dataset.offset = initialCount;
    }

    btn.addEventListener("click", async () => {
      const mode = btn.dataset.mode || "more";

      // ---------- SHOW LESS ----------
      if (mode === "less") {
        // remove everything after the initial items
        while (list.children.length > initialCount) {
          list.removeChild(list.lastElementChild);
        }
        btn.dataset.mode = "more";
        btn.textContent = "Show more";
        btn.style.display = "";
        return;
      }

      // ---------- SHOW MORE ----------
      const offset = parseInt(btn.dataset.offset || String(initialCount), 10);
      const limit = parseInt(btn.dataset.limit || String(defaultLimit), 10);

      try {
        const res = await fetch(`${url}?offset=${offset}&limit=${limit}`);
        if (!res.ok) {
          if (handleNonOk && handleNonOk(res, btn) === false) return;
          throw new Error(`Request failed with status ${res.status}`);
        }

        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0) {
          btn.style.display = "none";
          return;
        }

        data.forEach((item) => renderItem(item, list));

        const total = list.children.length;
        btn.dataset.offset = offset + data.length;

        // If we now have >= maxItems, switch button to "Show less"
        if (total >= maxItems) {
          btn.dataset.mode = "less";
          btn.textContent = "Show less";
          btn.style.display = "";
        } else if (data.length < limit) {
          // fewer than limit returned AND we didn't hit max -> no more data, hide
          btn.style.display = "none";
        }
      } catch (err) {
        console.error("Load-more error:", err);
      }
    });
  }

  // ---------- TRENDING ----------
  setupLoadMore({
    buttonId: "trendingShowMore",
    listId: "trendingList",
    // using existing underscore API endpoint
    url: "/api/_api-get-trending.php",
    defaultLimit: 2,
    renderItem(item, list) {
      const div = document.createElement("div");
      div.className = "trending-item";
      div.innerHTML = `
        <div class="trending-info">
          <span class="item_title">Trending · ${item.post_count} posts</span>
          <p>${item.topic}</p>
        </div>
        <span class="option">⋮</span>
      `;
      list.appendChild(div);
    },
  });

  // ---------- WHO TO FOLLOW ----------
  setupLoadMore({
    buttonId: "followShowMore",
    listId: "whoToFollowList",
    // using existing underscore API endpoint
    url: "/api/_api-get-who-to-follow.php",
    defaultLimit: 3,
    handleNonOk(res, btn) {
      if (res.status === 401) {
        console.warn("Not logged in");
        btn.style.display = "none";
        return false;
      }
      return true;
    },
    renderItem(user, list) {
      const a = document.createElement("a");
      a.href = `/user?user_pk=${user.user_pk}`;
      a.className = "profile-info";
      a.id = user.user_pk;

      const img = document.createElement("img");
      const seed = Math.abs((user.user_username || "").split("").reduce((acc, ch) => acc + ch.charCodeAt(0), 0) % 100);
      img.src = `https://avatar.iran.liara.run/public/${seed}`;
      img.alt = "Profile Picture";

      const info = document.createElement("div");
      info.className = "info-copy";
      const pName = document.createElement("p");
      pName.className = "name";
      pName.textContent = user.user_full_name;
      const pHandle = document.createElement("p");
      pHandle.className = "handle";
      pHandle.textContent = `@${user.user_username}`;
      info.append(pName, pHandle);

      const btnFollow = document.createElement("button");
      btnFollow.className = `follow-btn button-${user.user_pk}`;
      btnFollow.setAttribute("mix-get", `api-follow?user-pk=${user.user_pk}`);
      btnFollow.textContent = "Follow";

      a.append(img, info, btnFollow);
      list.appendChild(a);
    },
  });
});
