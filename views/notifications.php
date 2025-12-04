<?php
require_once __DIR__ . "/../x.php";

_ensureLogin('/');
$currentUser = _currentUser();
if (!$currentUser) {
        header('Location: /');
        exit();
}
require_once __DIR__ . "/../db.php";

// Fetch notifications for current user
$currentUserPk = $currentUser['user_pk'];
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
<?php
// page layout with header/footer components
$title = 'Notifications';
$currentPage = 'notifications';
require __DIR__ . '/../components/_header.php';
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
                      <img src="https://avatar.iran.liara.run/public/<?php echo crc32($n['user_username']) % 100; ?>" alt="avatar" class="avatar">
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

        <?php require __DIR__ . '/../components/_aside.php'; ?>

<?php require __DIR__ . '/../components/_footer.php'; ?>

