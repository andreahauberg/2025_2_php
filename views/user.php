<?php
require_once __DIR__ . "/../x.php";
session_start();
if (!isset($_SESSION["user"])) {
    header("location: /?message=not logged in, please login first");
    exit();
}
require_once __DIR__ . "/../db.php";

// Hent brugeren fra URL-parametre (f.eks. /user?user_pk=123)
$userPk = $_GET['user_pk'] ?? null;
if (!$userPk) {
    header("location: /home");
    exit();
}

// Hent den valgte bruger
$q = "SELECT * FROM users WHERE user_pk = :userPk";
$stmt = $_db->prepare($q);
$stmt->bindValue(":userPk", $userPk);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("location: /home");
    exit();
}

// Hent den loggede bruger
$currentUser = $_SESSION["user"];
$currentUserPk = $currentUser["user_pk"];

// Tjek om den loggede bruger følger denne bruger
$isFollowing = false;
if ($currentUserPk !== $userPk) {
    $q = "SELECT COUNT(*) FROM follows WHERE follower_user_fk = :followerPk AND follow_user_fk = :followUserPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':followerPk', $currentUserPk);
    $stmt->bindValue(':followUserPk', $userPk);
    $stmt->execute();
    $isFollowing = $stmt->fetchColumn() > 0;
}

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
  WHERE posts.post_user_fk = :userPk AND posts.deleted_at IS NULL
  ORDER BY posts.created_at DESC
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":userPk", $userPk);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tilføj like- og kommentar-data til posts
foreach ($posts as &$post) {
    // Hent like_count
    $q = "SELECT COUNT(*) AS like_count FROM likes WHERE like_post_fk = :postPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['like_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    // Hent om den loggede bruger har liket posten
    $q = "SELECT COUNT(*) AS is_liked FROM likes WHERE like_post_fk = :postPk AND like_user_fk = :userPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->bindValue(':userPk', $currentUserPk);
    $stmt->execute();
    $post['is_liked_by_user'] = $stmt->fetch(PDO::FETCH_ASSOC)['is_liked'] > 0;

    // Hent kommentar-tælleren
    $q = "SELECT COUNT(*) AS comment_count FROM comments WHERE comment_post_fk = :postPk AND deleted_at IS NULL";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['comment_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['comment_count'];
}
unset($post);

// 2. Hent brugerens følgere
$q = "
  SELECT DISTINCT users.*
  FROM follows
  JOIN users ON follows.follower_user_fk = users.user_pk
  WHERE follows.follow_user_fk = :userPk
  AND users.user_pk != :currentUserPk
  LIMIT 10
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":userPk", $userPk);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Hent forslag til brugere at følge
$q = "
  SELECT DISTINCT users.*
  FROM users
  WHERE users.user_pk != :currentUserPk
  AND users.user_pk != :userPk
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
$stmt->bindValue(":userPk", $userPk);
$stmt->execute();
$usersToFollow = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title><?php _($user["user_full_name"]); ?> (@<?php _($user["user_username"]); ?>)</title>
</head>
<body>
    <div id="container">
        <button class="burger" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
            <i class="fa-solid fa-xmark"></i>
        </button>
        <nav>
            <!-- Navigation -->
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
                        <?php _($currentUser["user_full_name"]); ?>
                    </div>
                    <div class="handle">
                        <?php _("@" . $currentUser["user_username"]); ?>
                    </div>
                </div>
                <i class="fa-solid fa-ellipsis option"></i>
            </div>
        </nav>
        <?php
            require_once __DIR__ . "/../components/_post-dialog.php";
        ?>
        <main>
            <div class="profile-header">
                <div class="profile-cover-container">
                    <img src="https://picsum.photos/600/200" alt="Cover" class="profile-cover">
                    <div class="profile-cover-filter"></div>
                </div>
                <div class="profile-page-info">
                    <img src="https://avatar.iran.liara.run/public/<?php echo crc32($user["user_username"]) % 100; ?>" alt="Profile" class="profile-avatar">
                    <div class="profile-details">
                        <h1><?php _($user["user_full_name"]); ?></h1>
                        <p>@<?php _($user["user_username"]); ?></p>
                        <div class="profile-stats">
                            <span><strong><?php echo count($posts); ?></strong> Posts</span>
                            <span><strong><?php echo count($followers); ?></strong> Followers</span>
                        </div>
                        <?php if ($userPk !== $currentUserPk): ?>
                            <div class="follow-button-container">
                                <?php if ($isFollowing): ?>
                                    <button
                                        class="unfollow-btn button-<?php echo $userPk; ?>"
                                        mix-get="api-unfollow?user-pk=<?php echo $userPk; ?>">
                                        Unfollow
                                    </button>
                                <?php else: ?>
                                    <button
                                        class="follow-btn button-<?php echo $userPk; ?>"
                                        mix-get="api-follow?user-pk=<?php echo $userPk; ?>">
                                        Follow
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
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
                <h2>Followers</h2>
    <?php if (empty($followers)): ?>
        <p>No followers yet.</p>
    <?php else: ?>
        <div class="follow-suggestion">
            <?php foreach ($followers as $follower): ?>
                <?php $user = $follower; ?>
                <?php require __DIR__ . "/../components/_follow_tag_user.php"; ?>
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
            <!-- Search overlay -->
        </div>
    </div>
    <script src="../public/js/mixhtml.js"></script>
</body>
</html>
