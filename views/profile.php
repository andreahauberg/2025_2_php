<?php
require_once __DIR__ . "/../x.php";

_ensureLogin('/');
$currentUser = _currentUser();
if (!$currentUser) {
    header('Location: /');
    exit();
}
require_once __DIR__ . "/../db.php";

$currentUser = $_SESSION["user"];
$currentUserPk = $currentUser["user_pk"];

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
    WHERE posts.post_user_fk = :currentUserPk AND posts.deleted_at IS NULL
        AND users.deleted_at IS NULL
  ORDER BY posts.created_at DESC
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../models/CommentModel.php';
$commentModel = new CommentModel();
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
    try {
        $post['comment_count'] = $commentModel->countForPost($post['post_pk']);
    } catch (Exception $_) {
        $post['comment_count'] = 0;
    }
}
unset($post);

$q = "
  SELECT users.*
  FROM follows
  JOIN users ON follows.follow_user_fk = users.user_pk
  WHERE follows.follower_user_fk = :currentUserPk
    AND users.deleted_at IS NULL
        LIMIT 3
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$following = $stmt->fetchAll();

$q = "
  SELECT users.*
  FROM users
  WHERE users.user_pk != :currentUserPk
    AND users.user_pk NOT IN (
    SELECT follow_user_fk
    FROM follows
    WHERE follower_user_fk = :currentUserPk
  )
    AND users.deleted_at IS NULL
  ORDER BY RAND()
    LIMIT 3
";
$stmt = $_db->prepare($q);
$stmt->bindValue(":currentUserPk", $currentUserPk);
$stmt->execute();
$usersToFollow = $stmt->fetchAll();
?>
<?php
$title = 'Profile: ' . ($currentUser['user_full_name'] ?? '');
$currentPage = 'profile';
require __DIR__ . '/../components/_header.php';
?>

<main>

    <div class="profile-header">

        <div class="profile-cover-container">
        <img 
    src="<?php echo !empty($currentUser['user_cover']) ? $currentUser['user_cover'] : 'https://picsum.photos/800/300'; ?>" 
    alt="Cover" 
    class="profile-cover"
>
<form action="/api/api-upload-image.php"
      method="POST"
      enctype="multipart/form-data"
      class="cover-upload-form">
    <input type="hidden" name="type" value="cover">

    <label class="cover-upload-btn">
        <i class="fa-solid fa-camera"></i>
        <input type="file" name="file" accept="image/*" hidden>
    </label>

    <button type="submit" class="cover-save-btn" style="display:none;">
        Save
    </button>
</form>
            <div class="profile-cover-filter"></div>
        </div>
        <div class="profile-page-info">


            <form action="/api/api-upload-image.php"
                  method="POST"
                  enctype="multipart/form-data"
                  class="avatar-upload-form">
                <input type="hidden" name="type" value="avatar">

                <div class="avatar-wrapper">
                    <img src="<?php echo !empty($currentUser['user_avatar']) ? $currentUser['user_avatar'] : '/public/img/avatar.jpg'; ?>"
                         alt="Profile" class="profile-avatar">

                    <label class="avatar-edit-btn" style="position:absolute; bottom:0; right:0; z-index:25; background:black; padding:6px; border-radius:50%;">
                        <i class="fa-solid fa-camera"></i>
                        <input type="file" name="file" accept="image/*" hidden>
                    </label>
                </div>
            </form>
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
<script src="/public/js/profile.js"></script>
</main>

        <?php
        // ensure aside uses same follow limit
        $followLimit = $followLimit ?? 3;
        require __DIR__ . '/../components/_aside.php'; ?>

<?php require __DIR__ . '/../components/_footer.php'; ?>