<?php
require_once __DIR__ . "/../x.php";
// Ensure the user is logged in using helper function from x.php
_ensureLogin('/');
// get current user via helper from x.php
$user = _currentUser();

require_once __DIR__ . "/../db.php";
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/TrendingModel.php';
require_once __DIR__ . '/../models/FollowModel.php';

$postModel     = new PostModel();
$trendingModel = new TrendingModel();
$followModel   = new FollowModel();

$currentUser = $_SESSION["user"]["user_pk"];

$posts = $postModel->getPostsForFeed(50);

$limit    = 4;
$offset   = 0;
$maxItems = 10;

$trending = $trendingModel->getTrending($limit, $offset);

$followLimit = 3;
$usersToFollow = $followModel->getSuggestions($currentUser, $followLimit, 0);
$initialFollowCount = count($usersToFollow);
?>

<?php
$title = 'Home';
$currentPage = 'home';
require __DIR__ . '/../components/_header.php';
?>
    <?php // reforged - header now provides the container, burger and nav ?>
    <?php // dialogs and overlays are loaded in the footer component ?>

        <main>
            <?php foreach ($posts as $post): ?>
            <?php require __DIR__ . "/../components/_post.php"; ?>
            <?php endforeach; ?>
        </main>

        <?php require __DIR__ . '/../components/_aside.php'; ?>
    
<?php require __DIR__ . '/../components/_footer.php'; ?>
