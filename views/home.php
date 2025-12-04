<?php
require_once __DIR__ . "/../x.php";
// _noCache();
session_start();

if (!isset($_SESSION["user"])) {
    _toastError('Not logged in, please login first');
    header('Location: /');
    exit();
}

require_once __DIR__ . "/../db.php";
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/TrendingModel.php';
require_once __DIR__ . '/../models/FollowModel.php';

$postModel     = new PostModel();
$trendingModel = new TrendingModel();
$followModel   = new FollowModel();

$currentUser = $_SESSION["user"]["user_pk"];

$posts = $postModel->getPostsForFeed(50);

$limit    = 4;
$offset   = 0;
$maxItems = 10;

$trending = $trendingModel->getTrending($limit, $offset);

$followLimit = 3;
$usersToFollow = $followModel->getSuggestions($currentUser, $followLimit, 0);
$initialFollowCount = count($usersToFollow);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../public/favicon/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/app.css">
    <link rel="stylesheet" href="../public/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script type="module" src="../public/js/app.js"></script>
    <script defer src="../public/js/dialog.js"></script>
    <script defer src="../public/js/comment.js"></script>
    <script defer src="../public/js/confirm-delete.js"></script>
    <script defer src="../public/js/load-more-btn.js"></script>
    <script defer src="../public/js/notifications.js"></script>
    <title>Welcome home <?php echo $_SESSION["user"]["user_username"]; ?></title>
</head>

<body>
    <?php require_once __DIR__ . "/../components/___toast.php"; ?>
    <div id="container">
        <button class="burger" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
            <i class="fa-solid fa-xmark"></i>
        </button>
        <nav>
            <ul>
            <li>
                    <a href="/home">
                        <img src="/public/favicon/favicon.ico" alt="Logo" class="nav-logo">
                    </a>
                </li>
                <li><a href="#"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
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
        require_once __DIR__ . "/../components/_post-dialog.php";
        require_once __DIR__ . "/../components/_update-profile-dialog.php";
        require_once __DIR__ . "/../components/_update-post-dialog.php";
        ?>

        <main>
            <?php foreach ($posts as $post): ?>
            <?php require __DIR__ . "/../components/_post.php"; ?>
            <?php endforeach; ?>
        </main>

        <aside>
            <form id="home-search-form">
                <input
                    id="home-search-input"
                    type="text"
                    placeholder="Search Weave"
                    autocomplete="off">
                <button type="submit">Search</button>
            </form>

            <div class="happening-now">
                <h2>What's happening now</h2>

                <?php
    $trending = $trendingModel->getTrending($limit, $offset);
    require __DIR__ . '/../components/_trending.php';
    ?>

                <?php if ($limit < $maxItems): ?>
                <button id="trendingShowMore" class="show-more-btn" data-offset="<?= $limit ?>" data-limit="2"
                    data-initial="<?= $limit ?>" data-max="<?= $maxItems ?>">
                    Show more
                </button>
                <?php endif; ?>
            </div>

            <hr>

            <div class="who-to-follow">
                <h2>Who to follow</h2>
                <?php if (empty($usersToFollow)): ?>
                    <p>No more users to follow.</p>
                <?php else: ?>
                <div class="follow-suggestion" id="whoToFollowList">
                    <?php foreach ($usersToFollow as $user): ?>
                    <?php require __DIR__ . "/../components/_follow_tag.php"; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($initialFollowCount === $followLimit): ?>
                    <button
                        id="followShowMore"
                        class="show-more-btn"
                        data-offset="<?= $initialFollowCount ?>"
                        data-limit="3"
                        data-initial="<?= $initialFollowCount ?>"
                        data-max="10"
                    >
                        Show more
                    </button>
                <?php endif; ?>
            </div>
        </aside>
    </div>
    <?php require_once __DIR__ . "/../components/_search.php"; ?>
    <script src="../public/js/mixhtml.js"></script>
</body>

</html>
