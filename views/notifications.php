<?php
require_once __DIR__ . "/../x.php";
session_start();
_ensureLogin('/');

require_once __DIR__ . "/../db.php";

// Fetch notifications for current user
$currentUserPk = $_SESSION['user']['user_pk'];
$q = "
  SELECT
    n.notification_pk,
    n.notification_message,
    n.is_read,
    n.created_at,
        n.notification_post_fk,
    u.user_full_name,
    u.user_username,
    u.user_pk AS actor_pk
  FROM notifications n
  JOIN users u ON n.notification_actor_fk = u.user_pk
  WHERE n.notification_user_fk = :user
  AND n.notification_post_fk IS NOT NULL
  ORDER BY n.created_at DESC
";
$stmt = $_db->prepare($q);
$stmt->bindValue(':user', $currentUserPk);
$stmt->execute();
$notifications = $stmt->fetchAll();

// small helper for time
function timeAgo($ts){
    try{ $d = new DateTime($ts); }catch(Exception $e){ return ''; }
    $diff = (new DateTime())->getTimestamp() - $d->getTimestamp();
    if($diff < 60) return $diff . 's';
    if($diff < 3600) return floor($diff/60) . 'm';
    if($diff < 86400) return floor($diff/3600) . 'h';
    return floor($diff/86400) . 'd';
}

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
    <script defer src="../public/js/notifications.js"></script>
    <title>Notifications</title>
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
                <li><a href="/home"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                <li><a href="#" class="open-search"><i class="fa-solid fa-magnifying-glass"></i><span>Explore</span></a></li>
                <li><a href="/notifications"><i class="fa-regular fa-bell"></i><span>Notifications</span></a></li>
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
                <img src="/public/img/avatar.jpg" alt="Profile">
                <div>
                    <div class="name">
                        <?php _($_SESSION['user']['user_full_name']); ?>
                    </div>
                    <div class="handle">
                        <?php _("@" . $_SESSION['user']['user_username']); ?>
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
            <div class="notifications-header" style="padding:1rem 1rem;">
                <h2 style="margin:0;">Notifications</h2>
                <button id="markAllBtn" class="mark-all-btn">Mark all as read</button>
            </div>
            <?php if (empty($notifications)): ?>
                <p style="padding:1rem; color:#657786;">No notifications.</p>
            <?php else: ?>
                <div class="notifications-list">
                <?php foreach ($notifications as $n): ?>
                <?php $unread = ($n['is_read'] == 0); ?>
                <div class="post <?php if($unread) echo 'notification--unread'; ?> notification-row" data-notif-pk="<?php echo $n['notification_pk']; ?>">
                    <a href="/user?user_pk=<?php echo $n['actor_pk']; ?>&post_pk=<?php echo $n['notification_post_fk']; ?>#post-<?php echo $n['notification_post_fk']; ?>" class="notif-link notif-link--row">
                      <img src="        <img src="/public/img/avatar.jpg" alt="Profile Picture" class="avatar">.<img src="/public/img/avatar.jpg" alt="Profile Picture" class="avatar">.liara.run/public/<?php echo crc32($n['user_username']) % 100; ?>" alt="avatar" class="avatar">
                      <div class="post-content">
                          <div class="post-header">
                              <span class="name"><?php _($n['user_full_name']); ?></span>
                              <span class="handle"><?php _("@" . $n['user_username']); ?></span>
                              <span class="time"><?php echo timeAgo($n['created_at']); ?></span>
                          </div>
                          <div class="text">
                              <?php _($n['notification_message']); ?>
                          </div>
                      </div>
                    </a>
                    <div class="notif-actions">
                        <button class="notif-mark-btn" data-notif-pk="<?php echo $n['notification_pk']; ?>">Mark</button>
                        <button class="notif-delete-btn" data-notif-pk="<?php echo $n['notification_pk']; ?>">Delete</button>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <aside>
            <form id="home-search-form">
                <input id="home-search-input" type="text" placeholder="Search Weave" autocomplete="off">
                <button type="submit">Search</button>
            </form>

            <div class="happening-now">
                <h2>What's happening now</h2>
                <?php
                require_once __DIR__ . '/../models/TrendingModel.php';
                $trendingModel = new TrendingModel();
                $trending = $trendingModel->getTrending(4,0);
                require __DIR__ . '/../components/_trending.php';
                ?>
            </div>

            <hr>

            <div class="who-to-follow">
                <h2>Who to follow</h2>
                <?php
                require_once __DIR__ . '/../models/FollowModel.php';
                $followModel = new FollowModel();
                $usersToFollow = $followModel->getSuggestions($currentUserPk, 3, 0);
                if (empty($usersToFollow)):
                ?>
                    <p>No suggestions.</p>
                <?php else: ?>
                    <div class="follow-suggestion">
                        <?php foreach ($usersToFollow as $user): ?>
                            <?php require __DIR__ . "/../components/_follow_tag.php"; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
