document.addEventListener("DOMContentLoaded", () => {
  function setupLoadMore({ buttonId, listId, url, defaultLimit, handleNonOk, renderItem }) {
    const btn = document.getElementById(buttonId);
    const list = document.getElementById(listId);
    if (!btn || !list) return;

    const initialCount = parseInt(btn.dataset.initial || list.children.length || "0", 10);
    const maxItems = parseInt(btn.dataset.max || "10", 10);

    btn.dataset.mode = btn.dataset.mode || "more";
    if (!btn.dataset.offset) {
      btn.dataset.offset = initialCount;
    }

    btn.addEventListener("click", async () => {
      const mode = btn.dataset.mode || "more";

      if (mode === "less") {
        while (list.children.length > initialCount) {
          list.removeChild(list.lastElementChild);
        }
        btn.dataset.mode = "more";
        btn.textContent = "Show more";
        btn.style.display = "";
        return;
      }

      const offset = parseInt(btn.dataset.offset || String(initialCount), 10);
      const limit = parseInt(btn.dataset.limit || String(defaultLimit), 10);

      try {
        // build url and include optional user_pk from the button dataset
        let queryUrl = url;
        const hasQuery = queryUrl.indexOf("?") !== -1;
        queryUrl += (hasQuery ? "&" : "?") + `offset=${offset}&limit=${limit}`;
        if (btn.dataset.userPk) {
          queryUrl += `&user_pk=${encodeURIComponent(btn.dataset.userPk)}`;
        }

        const res = await fetch(queryUrl);
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
        // ensure any mix-get/mix-post attributes on newly appended nodes are initialized
        if (typeof window.mix_convert === "function") {
          try {
            window.mix_convert();
          } catch (e) {
            console.warn("mix_convert failed", e);
          }
        }

        const total = list.children.length;
        btn.dataset.offset = offset + data.length;

        if (total > initialCount) {
          btn.dataset.mode = "less";
          btn.textContent = "Show less";
          btn.style.display = "";
        } else if (total >= maxItems) {
          // fallback
          btn.dataset.mode = "less";
          btn.textContent = "Show less";
          btn.style.display = "";
        } else if (data.length < limit) {
          btn.style.display = "none";
        }
      } catch (err) {
        console.error("Load-more error:", err);
      }
    });
  }

  setupLoadMore({
    buttonId: "trendingShowMore",
    listId: "trendingList",
    url: "/api/_api-get-trending.php",
    defaultLimit: 2,
    renderItem(item, list) {
      const raw = item.topic || "";
      const tag = raw.trim();
      const clean = tag.startsWith("#") ? tag.slice(1) : tag;

      const div = document.createElement("div");
      div.className = "trending-item";

      div.innerHTML = `
        <div class="trending-info">
          <span class="item_title">Trending · ${item.post_count} posts</span>
          <p>
            <a href="/hashtag/${clean}" class="hashtag-link">${tag}</a>
          </p>
        </div>
        <span class="option">⋮</span>
      `;

      list.appendChild(div);
    },
  });

  // renderers for follow items
  function renderFollowSuggestion(user, list) {
    const a = document.createElement("a");
    a.href = `/user?user_pk=${user.user_pk}`;
    a.className = "profile-info";
    a.id = user.user_pk;

    const img = document.createElement("img");
    img.src = user.user_avatar || "/public/img/avatar.jpg";
    img.className = "profile-avatar";

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
  }

  function renderFollowingItem(user, list) {
    const a = document.createElement("a");
    a.href = `/user?user_pk=${user.user_pk}`;
    a.className = "profile-info";
    a.id = user.user_pk;

    const img = document.createElement("img");
    img.src = user.user_avatar || "/public/img/avatar.jpg";
    img.className = "profile-avatar";

    const info = document.createElement("div");
    info.className = "info-copy";
    const pName = document.createElement("p");
    pName.className = "name";
    pName.textContent = user.user_full_name;
    const pHandle = document.createElement("p");
    pHandle.className = "handle";
    pHandle.textContent = `@${user.user_username}`;
    info.append(pName, pHandle);

    const btnUnfollow = document.createElement("button");
    btnUnfollow.className = `unfollow-btn button-${user.user_pk}`;
    btnUnfollow.setAttribute("mix-get", `api-unfollow?user-pk=${user.user_pk}`);
    btnUnfollow.textContent = "Unfollow";

    a.append(img, info, btnUnfollow);
    list.appendChild(a);
  }

  setupLoadMore({
    buttonId: "followShowMore",
    listId: "whoToFollowList",
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
    renderItem: renderFollowSuggestion,
  });

  // Load more for profile following list
  setupLoadMore({
    buttonId: "followingShowMore",
    listId: "followingList",
    url: "/api/_api-get-following.php",
    defaultLimit: 3,
    handleNonOk(res, btn) {
      if (res.status === 401) {
        btn.style.display = "none";
        return false;
      }
      return true;
    },
    renderItem: renderFollowingItem,
  });

  // Load more for followers on a user profile
  setupLoadMore({
    buttonId: "followersShowMore",
    listId: "followersList",
    url: "/api/_api-get-followers.php",
    defaultLimit: 3,
    renderItem: renderFollowSuggestion,
  });
});
