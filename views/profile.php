<?php
require_once __DIR__ . "/../x.php";
session_start();
if (!isset($_SESSION["user"])) {
    header("location: /?message=not logged in, please login first");
    exit();
}
require_once __DIR__ . "/../db.php";

// Hent den loggede bruger
$currentUser = $_SESSION["user"];
$currentUserPk = $currentUser["user_pk"];

// 1. Hent brugerens posts
$q = "
  SELECT
    posts.post_pk,
    posts.post_message,
    posts.post_image_path,
    posts.post_user_fk,
    posts.created_at,
    users.user_full_name,
    users.user_username,
    users.user_pk AS author_user_pk
  FROM posts
  JOIN users ON posts.post_user_fk = users.user_pk
  WHERE posts.post_user_fk = :currentUserPk AND posts.deleted_at IS NULL
  ORDER BY posts.created_at DESC
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$posts = $stmt->fetchAll();

// Tilføj like-data til posts
foreach ($posts as &$post) {
    $q = "SELECT COUNT(*) AS like_count FROM likes WHERE like_post_fk = :postPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['like_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    $q = "SELECT COUNT(*) AS is_liked FROM likes WHERE like_post_fk = :postPk AND like_user_fk = :userPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->bindValue(':userPk', $currentUserPk);
    $stmt->execute();
    $post['is_liked_by_user'] = $stmt->fetch(PDO::FETCH_ASSOC)['is_liked'] > 0;
}
unset($post);

// 2. Hent brugere, som den loggede bruger følger
$q = "
  SELECT users.*
  FROM follows
  JOIN users ON follows.follow_user_fk = users.user_pk
  WHERE follows.follower_user_fk = :currentUserPk
  LIMIT 10
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$following = $stmt->fetchAll();

// 3. Hent forslag til brugere at følge (samme logik som forsiden)
$q = "
  SELECT users.*
  FROM users
  WHERE users.user_pk != :currentUserPk
  AND users.user_pk NOT IN (
    SELECT follow_user_fk
    FROM follows
    WHERE follower_user_fk = :currentUserPk
  )
  ORDER BY RAND()
  LIMIT 3
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$usersToFollow = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../public/favicon.ico">
    <link rel="stylesheet" href="../public/css/app.css">
    <link rel="stylesheet" href="../public/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="../public/js/app.js"></script>
    <script defer src="../public/js/dialog.js"></script>
    <script defer src="../public/js/flip-btn.js"></script>
    <script defer src="../public/js/comment.js"></script>
    <script defer src="../public/js/confirm-delete.js"></script>
    <title>Profile: <?php echo htmlspecialchars($currentUser["user_full_name"]); ?></title>
</head>
<body>
    <div id="container">
        <button class="burger" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
            <i class="fa-solid fa-xmark"></i>
        </button>
        <nav>
            <ul>
                <li><a href="/home"><i class="fab fa-twitter"></i></a></li>
                <li><a href="/home"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                <li><a href="#" class="open-search"><i class="fa-solid fa-magnifying-glass"></i><span>Explore</span></a></li>
                <li><a href="#"><i class="fa-regular fa-bell"></i><span>Notifications</span></a></li>
                <li><a href="#"><i class="fa-regular fa-envelope"></i><span>Messages</span></a></li>
                <li><a href="#"><i class="fa-solid fa-atom"></i><span>Grok</span></a></li>
                <li><a href="#"><i class="fa-regular fa-bookmark"></i><span>Bookmarks</span></a></li>
                <li><a href="#"><i class="fa-solid fa-briefcase"></i><span>Jobs</span></a></li>
                <li><a href="#"><i class="fa-solid fa-users"></i><span>Communities</span></a></li>
                <li><a href="#"><i class="fa-solid fa-star"></i><span>Premium</span></a></li>
                <li><a href="/profile"><i class="fa-regular fa-user"></i><span>Profile</span></a></li>
                <li><a href="#" data-open="updateProfileDialog"><i class="fa-solid fa-ellipsis"></i><span>More</span></a></li>
                <li><a href="bridge-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
            </ul>
            <button class="post-btn" data-open="postDialog">Post</button>

            <div id="profile_tag" data-open="updateProfileDialog">
                <img src="https://avatar.iran.liara.run/public/<?php echo crc32($currentUser["user_username"]) % 100; ?>" alt="Profile">
                <div>
                    <div class="name">
                        <?php echo htmlspecialchars($currentUser["user_full_name"]); ?>
                    </div>
                    <div class="handle">
                        <?php echo htmlspecialchars("@" . $currentUser["user_username"]); ?>
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

            <div class="profile-header">
                <div class="profile-cover-container">
                    <img src="https://picsum.photos/600/200" alt="Cover" class="profile-cover">
                    <div class="profile-cover-filter"></div>
                </div>
                <div class="profile-page-info">
                    <img src="https://avatar.iran.liara.run/public/<?php echo crc32($currentUser["user_username"]) % 100; ?>" alt="Profile" class="profile-avatar">
                    <div class="profile-details">
                        <h1><?php echo htmlspecialchars($currentUser["user_full_name"]); ?></h1>
                        <p>@<?php echo htmlspecialchars($currentUser["user_username"]); ?></p>
                        <div class="profile-stats">
                            <span><strong><?php echo count($posts); ?></strong> Posts</span>
                            <span><strong><?php echo count($following); ?></strong> Following</span>
                        </div>
                    </div>
                </div>

            <?php if (empty($posts)): ?>
                <p class="no-posts">No posts yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <?php require __DIR__ . "/../components/_post.php"; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <aside>
            <form action="">
            <input id="profile-search-input" type="text" placeholder="Search Twitter" autocomplete="off">
                <button>Search</button>
            </form>

            <div class="following">
                <h2>Following</h2>
                <?php if (empty($following)): ?>
                    <p>Not following anyone yet.</p>
                <?php else: ?>
                    <div class="follow-suggestion">
                        <?php foreach ($following as $user): ?>
                            <?php require __DIR__ . "/../components/_follow_tag.php"; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
        <button type="button" class="search-overlay-close" aria-label="Close search">&times;</button>
        <form id="searchOverlayForm" class="search-overlay-form">
            <input id="searchOverlayInput" type="text" name="query" placeholder="Search" class="search-overlay-input" autocomplete="off">
            <button type="submit" class="search-overlay-btn">Search</button>
        </form>
        <div id="searchOverlayResults" class="search-overlay-results"></div>
    </div>
</div>
    </div>
    <script src="../public/js/mixhtml.js"></script>
</body>
</html>
