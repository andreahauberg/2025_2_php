<?php
// components/_nav.php includes the side navigation menu and profile tag and post button
require_once __DIR__ . '/../x.php';
$user = _currentUser();

// accept optional layout variables
$currentPage = $currentPage ?? '';

$notifCount = $notifCount ?? 0;
// if no notifCount provided, try to get it here
if ($notifCount === 0 && $user) {
    try {
        require_once __DIR__ . '/../models/NotificationModel.php';
        $nm = new NotificationModel();
        if (method_exists($nm, 'countUnreadForUser')) {
            $notifCount = $nm->countUnreadForUser($user['user_pk']);
        } else {
            $notifCount = 0;
        }
    } catch (Throwable $e) {
        $notifCount = 0;
    }
}
?>
<nav>
    <ul>
        <li>
            <a href="/home">
                <img src="/public/img/weave-logo.png" alt="Weave Logo" class="nav-logo">
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
        <li><a href="/bridge-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
    </ul>

    <?php if ($user): ?>
    <button class="post-btn" data-open="postDialog">Post</button>

    <div id="profile_tag" data-open="updateProfileDialog">
        <img src="https://avatar.iran.liara.run/public/<?php echo crc32($user['user_username'] ?? '') % 100; ?>"
            alt="Profile">
        <div>
            <div class="name"><?= htmlspecialchars($user['user_full_name'] ?? '') ?></div>
            <div class="handle"><?= $user ? ('@' . htmlspecialchars($user['user_username'])) : '' ?></div>
        </div>
        <i class="fa-solid fa-ellipsis option"></i>
    </div>
    <?php endif; ?>
</nav>