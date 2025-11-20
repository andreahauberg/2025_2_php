<?php
require_once __DIR__ . "/../x.php";
// _noCache();
session_start();

if (!isset($_SESSION["user"])) { 
    header("location: /?message=not logged in, please login first");
    exit();
}

require_once __DIR__ . "/../db.php";

$q = "
  SELECT
    posts.post_pk,
    posts.post_message,
    posts.post_image_path,
    posts.post_user_fk,
    users.user_full_name,
    users.user_username,
    users.user_pk AS author_user_pk
  FROM posts
  JOIN users ON posts.post_user_fk = users.user_pk
  ORDER BY RAND()
  LIMIT 10
";
$stmt = $_db->prepare($q);
$stmt->execute();
$posts = $stmt->fetchAll();


$currentUser = $_SESSION["user"]["user_pk"];

foreach ($posts as &$post) {
    // Hent like_count
    $q = "SELECT COUNT(*) AS like_count FROM likes WHERE like_post_fk = :postPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['like_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    // Hent om brugeren har liket posten
    $q = "SELECT COUNT(*) AS is_liked FROM likes WHERE like_post_fk = :postPk AND like_user_fk = :userPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->bindValue(':userPk', $currentUser);
    $stmt->execute();
    $post['is_liked_by_user'] = $stmt->fetch(PDO::FETCH_ASSOC)['is_liked'] > 0;
}
unset($post);



$q = "
  SELECT users.*
  FROM users
  WHERE users.user_pk != :currentUser
  AND users.user_pk NOT IN (
    SELECT follow_user_fk
    FROM follows
    WHERE follower_user_fk = :currentUser
  )
  ORDER BY RAND()
  LIMIT 3
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUser", $currentUser);
$stmt->execute();
$users = $stmt->fetchAll();
?>



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/app.css">
    <link rel="stylesheet" href="../public/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script type="module" src="../public/js/app.js"></script>
    <script defer src="../public/js/dialog.js"></script>
    <title>Welcome home <?php echo $_SESSION["user"]["user_username"]; ?></title>
</head>

<body>
    <div id="container">
        <button class="burger" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
            <i class="fa-solid fa-xmark"></i>
        </button>
        <nav>
            <ul>
                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                <li><a href="#search"><i class="fa-solid fa-magnifying-glass"></i><span>Explore</span></a></li>
                <li><a href="#"><i class="fa-regular fa-bell"></i><span>Notifications</span></a></li>
                <li><a href="#"><i class="fa-regular fa-envelope"></i><span>Messages</span></a></li>
                <li><a href="#"><i class="fa-solid fa-atom"></i><span>Grok</span></a></li>
                <li><a href="#"><i class="fa-regular fa-bookmark"></i><span>Bookmarks</span></a></li>
                <li><a href="#"><i class="fa-solid fa-briefcase"></i><span>Jobs</span></a></li>
                <li><a href="#"><i class="fa-solid fa-users"></i><span>Communities</span></a></li>
                <li><a href="#"><i class="fa-solid fa-star"></i><span>Premium</span></a></li>
                <li><a href="#"><i class="fa-solid fa-bolt"></i><span>Verified Orgs</span></a></li>
                <li><a href="/profile"><i class="fa-regular fa-user"></i><span>Profile</span></a></li>
                <li><a href="#" data-open="updateProfileDialog"><i class="fa-solid fa-ellipsis"></i><span>More</span></a></li>
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
            ?>
        

        <main>
            <?php //<span class="action flip-btn"><i class="fa-regular fa-heart"></i></span> 
            ?>
            <?php
            foreach ($posts as $post):
                require __DIR__ . "/../components/_post.php";
            endforeach
            ?>

        </main>
        <aside>
        <form id="home-search-form">
        <input
            id="home-search-input"
            type="text"
            placeholder="Search Twitter"
            autocomplete="off"
        >
        <button type="submit">Search</button>
    </form>
            <div class="happening-now">
                <h2>What's happening now</h2>
                <div class="trending">
                    <div class="trending-item">
                        <div class="trending-info">
                            <span class="item_title">Trending in Denmark</span>
                            <p>Grækenland</p>
                        </div>
                        <span class="option">⋮</span>
                    </div>
                    <div class="trending-item">
                        <div class="trending-info">
                            <span class="item_title">Politics . Trending</span>
                            <p>Syria</p>
                        </div>
                        <span class="option">⋮</span>
                    </div>
                    <div class="trending-item">
                        <div class="trending-info">
                            <span class="item_title">Bussiness . Trending</span>
                            <p>#AlgoTrading</p>
                        </div>
                        <span class="option">⋮</span>
                    </div>
                    <div class="trending-item">
                        <div class="trending-info">
                            <span class="item_title">Trending in Denmark</span>
                            <p>Italien</p>
                        </div>
                        <span class="option">⋮</span>
                    </div>
                    <button class="show-more-btn">Show more</button>
                </div>

            </div>
            <hr>
            <div class="who-to-follow">
                <h2>Who to follow</h2>
                <?php if (empty($usersToFollow)): ?>
                    <p>No more users to follow.</p>
                <?php else: ?>
                    <div class="follow-suggestion">
                        <?php foreach ($usersToFollow as $user): ?>
                            <?php require __DIR__ . "/../components/_follow_tag.php"; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                    <button class="show-more-btn">Show more</button>
                </div>
        </aside>
        <div class="search-overlay" aria-hidden="true">
    <div class="search-overlay-box">
        <button
            type="button"
            class="search-overlay-close"
            aria-label="Close search"
        >
            &times;
        </button>

        <form id="searchOverlayForm" class="search-overlay-form">
            <input
                id="searchOverlayInput"
                type="text"
                name="query"
                placeholder="Search"
                class="search-overlay-input"
                autocomplete="off"
            >
            <button type="submit" class="search-overlay-btn">Search</button>
        </form>

        <div id="searchOverlayResults" class="search-overlay-results">
        </div>
    </div>
</div>
    </div>
      <script src="../public/js/mixhtml.js"></script>
</body>

</html>