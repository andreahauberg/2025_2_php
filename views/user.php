<?php
require_once __DIR__ . "/../x.php";
_ensureLogin('/');
$currentUser = _currentUser();
if (!$currentUser) {
    header('Location: /');
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
$profileUser = $stmt->fetch();

if (!$profileUser) {
    header("location: /home");
    exit();
}

// Hent den loggede bruger
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
    users.user_pk AS author_user_pk,
    users.user_avatar
  FROM posts
  JOIN users ON posts.post_user_fk = users.user_pk
  WHERE posts.post_user_fk = :userPk AND posts.deleted_at IS NULL
  ORDER BY posts.created_at DESC
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":userPk", $userPk);
$stmt->execute();
$posts = $stmt->fetchAll();

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

    $q = "SELECT COUNT(*) AS comment_count FROM comments WHERE comment_post_fk = :postPk AND deleted_at IS NULL";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['comment_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['comment_count'];
}
unset($post);

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
$followers = $stmt->fetchAll();

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
$usersToFollow = $stmt->fetchAll();

$title = $profileUser["user_full_name"] . " (@" . $profileUser["user_username"] . ")";
$currentPage = 'user';
require __DIR__ . '/../components/_header.php';
?>

<main>
    <div class="profile-header">
        <div class="profile-cover-container">
            <img src="https://picsum.photos/600/200" alt="Cover" class="profile-cover">
            <div class="profile-cover-filter"></div>
        </div>
        <div class="profile-page-info">
            <img src="<?php echo !empty($profileUser['user_avatar']) ? $profileUser['user_avatar'] : '/public/img/avatar.jpg'; ?>"
                alt="Profile" class="profile-avatar" />
            <div class="profile-details">
                <h1><?php _($profileUser["user_full_name"]); ?></h1>
                <p>@<?php _($profileUser["user_username"]); ?></p>
                <div class="profile-stats">
                    <span><strong><?php echo count($posts); ?></strong> Posts</span>
                    <span><strong><?php echo count($followers); ?></strong> Followers</span>
                </div>
                <?php if ($userPk !== $currentUserPk): ?>
                <div class="follow-button-container">
                    <?php if ($isFollowing): ?>
                    <button class="unfollow-btn button-<?php echo $userPk; ?>"
                        mix-get="api-unfollow?user-pk=<?php echo $userPk; ?>">
                        Unfollow
                    </button>
                    <?php else: ?>
                    <button class="follow-btn button-<?php echo $userPk; ?>"
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

<?php require __DIR__ . '/../components/_aside.php'; ?>

<?php require __DIR__ . '/../components/_footer.php'; ?>

<?php
