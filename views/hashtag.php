<?php
require_once __DIR__ . '/../x.php';

_ensureLogin('/');
$currentUser = _currentUser();
if (!$currentUser) {
    header('Location: /');
    exit();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/PostModel.php';

$tag = $_GET['tag'] ?? null;
if (!$tag) {
    echo "Invalid hashtag.";
    exit();
}

$postModel = new PostModel();
$posts = $postModel->getPostsByHashtag($tag);
$hashtag = "#" . htmlspecialchars($tag);

// Prefill aside search
$homeSearchValue = $hashtag;

// page meta for shared header/footer
$title = $hashtag;
$currentPage = 'hashtag';
require __DIR__ . '/../components/_header.php';
?>

        <main class="feed-column">
        <main class="feed-column">

            <h2 style="padding:15px;">Showing posts for <?= $hashtag ?></h2>
            <h2 style="padding:15px;">Showing posts for <?= $hashtag ?></h2>

            <?php if (empty($posts)): ?>
                <p style="padding:15px;">No posts found for <?= $hashtag ?>.</p>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <?php require __DIR__ . '/../components/_post.php'; ?>
            <?php endforeach; ?>

        </main>

        <?php require __DIR__ . '/../components/_aside.php'; ?>

        <?php require __DIR__ . '/../components/_footer.php'; ?>
