<?php
require_once __DIR__ . '/../x.php';
session_start();
_ensureLogin('/');

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/TrendingModel.php';

$trendingModel = new TrendingModel();
$trending = $trendingModel->getTrending(10);

$tag = $_GET['tag'] ?? null;
if (!$tag) {
    echo "Invalid hashtag.";
    exit();
}

$postModel = new PostModel();
$posts = $postModel->getPostsByHashtag($tag);
$hashtag = "#" . htmlspecialchars($tag);

$currentUser = $_SESSION["user"]["user_pk"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/public/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/public/css/app.css">
    <link rel="stylesheet" href="/public/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script type="module" src="/public/js/app.js"></script>
    <script defer src="/public/js/dialog.js"></script>
    <script defer src="/public/js/comment.js"></script>
    <script defer src="/public/js/confirm-delete.js"></script>
    <script defer src="/public/js/load-more-btn.js"></script>
    <script defer src="/public/js/notifications.js"></script>

    <title><?= $hashtag ?></title>
</head>

<body>
<?php require __DIR__ . '/../components/___toast.php'; ?>

<div id="container">
        <button class="burger" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
            <i class="fa-solid fa-xmark"></i>
        </button>
        <nav>
            <ul>
                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                <li><a href="/home"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                <li><a href="#" class="open-search"><i class="fa-solid fa-magnifying-glass"></i><span>Explore</span></a>
                </li>
                <li><a href="/notifications"><i class="fa-regular fa-bell"></i><span>Notifications</span></a></li>
                <li><a href="#"><i class="fa-regular fa-envelope"></i><span>Messages</span></a></li>
                <li><a href="#"><i class="fa-solid fa-atom"></i><span>Grok</span></a></li>
                <li><a href="#"><i class="fa-regular fa-bookmark"></i><span>Bookmarks</span></a></li>
                <li><a href="#"><i class="fa-solid fa-briefcase"></i><span>Jobs</span></a></li>
                <li><a href="#"><i class="fa-solid fa-users"></i><span>Communities</span></a></li>
                <li><a href="#"><i class="fa-solid fa-star"></i><span>Premium</span></a></li>
                <li><a href="#"><i class="fa-solid fa-bolt"></i><span>Verified Orgs</span></a></li>
                <li><a href="/profile"><i class="fa-regular fa-user"></i><span>Profile</span></a></li>
                <li><a href="#" data-open="updateProfileDialog"><i
                            class="fa-solid fa-ellipsis"></i><span>More</span></a></li>
                <li><a href="bridge-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
            </ul>

            <button class="post-btn" data-open="postDialog">Post</button>

            <div id="profile_tag" data-open="updateProfileDialog">
                <img src="https://avatar.iran.liara.run/public/73" alt="Profile">
                <div>
                    <div class="name">
                        <?php _($_SESSION["user"]["user_full_name"]); ?>
                    </div>
                    <div class="handle">
                        <?php _("@" . $_SESSION["user"]["user_username"]); ?>
                    </div>
                </div>
                <i class="fa-solid fa-ellipsis option"></i>
            </div>
        </nav>


    <?php
    require_once __DIR__ . '/../components/_post-dialog.php';
    require_once __DIR__ . '/../components/_update-profile-dialog.php';
    require_once __DIR__ . '/../components/_update-post-dialog.php';
    ?>

    <main class="feed-column">

        <h2 style="padding:15px;">Showing posts for <?= $hashtag ?></h2>

        <?php if (empty($posts)): ?>
            <p style="padding:15px;">No posts found for <?= $hashtag ?>.</p>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <?php require __DIR__ . '/../components/_post.php'; ?>
        <?php endforeach; ?>

    </main>
    <aside>

        <form id="home-search-form">
            <input
                id="home-search-input"
                type="text"
                placeholder="Search Twitter"
                autocomplete="off"
                value="<?= $hashtag ?>"
            >
            <button type="submit">Search</button>
        </form>

        <div class="happening-now">
            <h2>What's happening now</h2>
            <?php require __DIR__ . '/../components/_trending.php'; ?>
        </div>

    </aside>

</div>

<div class="search-overlay" aria-hidden="true">
    <div class="search-overlay-box">
        <button type="button" class="search-overlay-close">&times;</button>

        <form id="searchOverlayForm" class="search-overlay-form">
            <input id="searchOverlayInput" type="text" name="query" class="search-overlay-input" autocomplete="off">
            <button type="submit" class="search-overlay-btn">Search</button>
        </form>

        <div id="searchOverlayResults" class="search-overlay-results"></div>
    </div>
</div>

<script src="/public/js/mixhtml.js"></script>

</body>
</html>