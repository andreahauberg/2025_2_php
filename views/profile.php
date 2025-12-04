<?php
require_once __DIR__ . "/../x.php";

// helper to ensure the user is logged in and get current user
_ensureLogin('/');
$currentUser = _currentUser();
if (!$currentUser) {
    // fallback
    header('Location: /');
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

// Tilføj comment-og like data til posts
require_once __DIR__ . '/../models/CommentModel.php';
$commentModel = new CommentModel();
// Tilføj comment_count via modellen så view får server-side værdi
foreach ($posts as &$post) {
    $q = "SELECT COUNT(*) AS like_count FROM likes WHERE like_post_fk = :postPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->execute();
    $post['like_count'] = $stmt->fetch()['like_count'];

    $q = "SELECT COUNT(*) AS is_liked FROM likes WHERE like_post_fk = :postPk AND like_user_fk = :userPk";
    $stmt = $_db->prepare($q);
    $stmt->bindValue(':postPk', $post['post_pk']);
    $stmt->bindValue(':userPk', $currentUserPk);
    $stmt->execute();
    $post['is_liked_by_user'] = $stmt->fetch()['is_liked'] > 0;
    // server-side comment count
    try {
        $post['comment_count'] = $commentModel->countForPost($post['post_pk']);
    } catch (Exception $_) {
        $post['comment_count'] = 0;
    }
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
<?php
// Useing header/footer components 
$title = 'Profile: ' . ($currentUser['user_full_name'] ?? '');
$currentPage = 'profile';
require __DIR__ . '/../components/_header.php';
?>

        <main>

            <div class="profile-header">
                <div class="profile-cover-container">
                    <img src="https://picsum.photos/600/200" alt="Cover" class="profile-cover">
                    <div class="profile-cover-filter"></div>
                </div>
                <div class="profile-page-info">
                    <img src="/public/img/avatar.jpg" alt="Profile" class="profile-avatar">
                    <div class="profile-details">
                        <h1><?php _($currentUser["user_full_name"]); ?></h1>
                        <p>@<?php _($currentUser["user_username"]); ?></p>
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

        <?php require __DIR__ . '/../components/_aside.php'; ?>

        <?php require __DIR__ . '/../components/_footer.php'; ?>
