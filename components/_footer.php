  <footer class="x-landing__footer">
    © 2025 X Corp
  </footer>

  <?php
    // include dialog components if available
    if (file_exists(__DIR__ . '/_post-dialog.php')) require_once __DIR__ . '/_post-dialog.php';
    if (file_exists(__DIR__ . '/_update-profile-dialog.php')) require_once __DIR__ . '/_update-profile-dialog.php';
    if (file_exists(__DIR__ . '/_update-post-dialog.php')) require_once __DIR__ . '/_update-post-dialog.php';

    // shared overlays (search overlay etc.) — embedded here so overlays load last
  ?>
    <div class="search-overlay" aria-hidden="true">
      <div class="search-overlay-box">
        <button type="button" class="search-overlay-close" aria-label="Close search">&times;</button>
        <form id="searchOverlayForm" class="search-overlay-form">
          <input id="searchOverlayInput" type="text" name="query" placeholder="Search" class="search-overlay-input" autocomplete="off">
          <button type="submit" class="search-overlay-btn">Search</button>
        </form>
        <div id="searchOverlayResults" class="search-overlay-results"></div>
      </div>
    </div>
  <?php
  ?>

  <script defer src="/public/js/notifications.js"></script>
  <script src="/public/js/mixhtml.js"></script>
</body>
</html>