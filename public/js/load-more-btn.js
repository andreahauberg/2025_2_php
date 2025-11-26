document.addEventListener("DOMContentLoaded", () => {
  console.log("load-more-btn.js loaded");

  function getIntDataset(el, key, fallback) {
    return el ? parseInt(el.dataset[key], 10) || fallback : fallback;
  }

  async function fetchJson(url) {
    const res = await fetch(url);
    if (!res.ok) throw res;
    return res.json();
  }

  // Append a trending item (keeps markup simple and consistent)
  function appendTrendingItem(container, item) {
    const div = document.createElement("div");
    div.className = "trending-item";
    div.innerHTML = `
      <div class="trending-info">
        <span class="item_title">Trending · ${item.post_count} posts</span>
        <p>${item.topic}</p>
      </div>
      <span class="option">⋮</span>
    `;
    container.appendChild(div);
  }

  // Build the follow suggestion element matching `components/_follow_tag.php` structure
  function buildFollowNode(user) {
    const a = document.createElement("a");
    a.href = `/user?user_pk=${user.user_pk}`;
    a.className = "profile-info";
    a.id = user.user_pk;

    const seed = Math.abs((user.user_username || "").split("").reduce((acc, ch) => acc + ch.charCodeAt(0), 0) % 100);

    const img = document.createElement("img");
    img.src = `https://avatar.iran.liara.run/public/${seed}`;
    img.alt = "Profile Picture";

    const info = document.createElement("div");
    info.className = "info-copy";

    const pName = document.createElement("p");
    pName.className = "name";
    pName.textContent = user.user_full_name || user.user_username || "";

    const pHandle = document.createElement("p");
    pHandle.className = "handle";
    pHandle.textContent = `@${user.user_username || ""}`;

    info.appendChild(pName);
    info.appendChild(pHandle);

    const btn = document.createElement("button");
    btn.className = `follow-btn button-${user.user_pk}`;
    btn.setAttribute("mix-get", `api-follow?user-pk=${user.user_pk}`);
    btn.textContent = "Follow";

    a.appendChild(img);
    a.appendChild(info);
    a.appendChild(btn);

    return a;
  }

  // Generic loader for a section
  function wireShowMore(buttonId, listId, endpoint, { limitDefault = 2, handle401 = false, onAppend } = {}) {
    const btn = document.getElementById(buttonId);
    const list = document.getElementById(listId);
    if (!btn || !list) return;

    btn.addEventListener("click", async () => {
      const offset = getIntDataset(btn, "offset", 0);
      const limit = getIntDataset(btn, "limit", limitDefault);
      const url = `${endpoint}?offset=${offset}&limit=${limit}`;

      console.log(`Click ${buttonId}, offset=${offset}, limit=${limit}`, url);

      try {
        const res = await fetch(url);
        console.log(`${buttonId} response status:`, res.status);

        if (!res.ok) {
          if (handle401 && res.status === 401) {
            console.warn("Not logged in - hiding button");
            btn.style.display = "none";
            return;
          }
          throw new Error("Network response not ok: " + res.status);
        }

        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0) {
          btn.style.display = "none";
          return;
        }

        data.forEach((item) => {
          onAppend(list, item);
        });

        btn.dataset.offset = offset + data.length;
        if (data.length < limit) btn.style.display = "none";
      } catch (err) {
        console.error(`Failed to load more for ${buttonId}`, err);
      }
    });
  }

  // Wire trending (default limit 2)
  wireShowMore("trendingShowMore", "trendingList", "/api/_api-get-trending.php", {
    limitDefault: 2,
    onAppend: appendTrendingItem,
  });

  // Wire who-to-follow (default limit 3). Hide on 401.
  wireShowMore("followShowMore", "whoToFollowList", "/api/_api-get-who-to-follow.php", {
    limitDefault: 3,
    handle401: true,
    onAppend: (list, user) => list.appendChild(buildFollowNode(user)),
  });
});
