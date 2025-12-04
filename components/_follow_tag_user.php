<a href="/user?user_pk=<?php echo $user['user_pk']; ?>" class="profile-info" id="<?php echo $user["user_pk"]; ?>">
<img src="/public/img/avatar.jpg" alt="Profile Picture" class="avatar">
    <div class="info-copy">
        <p class="name"><?php _($user["user_full_name"]); ?></p>
        <p class="handle"><?php _("@" . $user["user_username"]); ?></p>
    </div>
    <?php
    $user_pk = $user["user_pk"];
    // Tjek om den loggede bruger følger denne bruger
    $isFollowing = false;
    if (isset($_SESSION["user"])) {
        require_once __DIR__ . '/../db.php';
        $q = "SELECT COUNT(*) FROM follows WHERE follower_user_fk = :followerPk AND follow_user_fk = :followPk";
        $stmt = $_db->prepare($q);
        $stmt->bindValue(':followerPk', $_SESSION["user"]["user_pk"]);
        $stmt->bindValue(':followPk', $user_pk);
        $stmt->execute();
        $isFollowing = $stmt->fetchColumn() > 0;
    }
    // Vis den korrekte knap
    if ($isFollowing) {
        require __DIR__ . '/___button_unfollow.php';
    } else {
        require __DIR__ . '/___button_follow.php';
    }
    ?>
</a>

