<?php
// Shared aside used on all pages including search, trending and who-to-follow
require_once __DIR__ . '/../x.php';
require_once __DIR__ . '/../models/TrendingModel.php';
require_once __DIR__ . '/../models/FollowModel.php';
require_once __DIR__ . '/../models/UserModel.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$currentUserPk = $_SESSION['user']['user_pk'] ?? null;

$trendingLimit = $trendingLimit ?? 4;
$trendingOffset = $trendingOffset ?? 0;
$maxItems = $maxItems ?? 10;

$trendingModel = new TrendingModel();
$trending = $trendingModel->getTrending($trendingLimit, $trendingOffset);

// who to follow if not already set
if (!isset($usersToFollow)) {
    $followLimit = $followLimit ?? 3;
    $followModel = new FollowModel();
    $usersToFollow = [];
    if ($currentUserPk) {
        $usersToFollow = $followModel->getSuggestions($currentUserPk, $followLimit, 0);
    }
}
$initialFollowCount = $initialFollowCount ?? count($usersToFollow);

?>
<aside>
    <form id="home-search-form">
        <input
            id="home-search-input"
            type="text"
            placeholder="Search Twitter"
            autocomplete="off">
        <button type="submit">Search</button>
    </form>

    <div class="happening-now">
        <h2>What's happening now</h2>

        <?php
        $trending = $trendingModel->getTrending($trendingLimit, $trendingOffset);
        require __DIR__ . '/_trending.php';
        ?>

        <?php if ($trendingLimit < $maxItems): ?>
        <button id="trendingShowMore" class="show-more-btn" data-offset="<?= $trendingLimit ?>" data-limit="2"
            data-initial="<?= $trendingLimit ?>" data-max="<?= $maxItems ?>">
            Show more
        </button>
        <?php endif; ?>
    </div>

    <hr>

    <div class="who-to-follow">
        <h2>Who to follow</h2>
        <?php if (empty($usersToFollow)): ?>
            <p>No more users to follow.</p>
        <?php else: ?>
        <div class="follow-suggestion" id="whoToFollowList">
            <?php foreach ($usersToFollow as $user): ?>
                <?php require __DIR__ . '/_follow_tag.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($initialFollowCount === ($followLimit ?? 3)): ?>
            <button
                id="followShowMore"
                class="show-more-btn"
                data-offset="<?= $initialFollowCount ?>"
                data-limit="3"
                data-initial="<?= $initialFollowCount ?>"
                data-max="10"
            >
                Show more
            </button>
        <?php endif; ?>
    </div>
</aside>


