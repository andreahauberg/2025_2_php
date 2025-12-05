<?php
require_once __DIR__ . '/../db.php';

$offset = $trendingOffset ?? 0;
$limit  = $trendingLimit ?? 4;

// 1. Hent alle posts med hashtags
$sql = "
    SELECT post_message
    FROM posts
    WHERE post_message REGEXP '#[A-Za-z0-9_]+'
      AND deleted_at IS NULL
";
$stmt = $_db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = [];

foreach ($rows as $row) {
    preg_match_all('/#[A-Za-z0-9_]+/', $row['post_message'], $matches);

    foreach ($matches[0] as $tag) {
        $tagLower = strtolower($tag);
        if (!isset($counts[$tagLower])) {
            $counts[$tagLower] = 0;
        }
        $counts[$tagLower]++;
    }
}

arsort($counts);

$allTags = array_keys($counts);
$sliced = array_slice($allTags, $offset, $limit);
?>

<div class="trending" id="trendingList">
<?php foreach ($trending as $row): ?>
    <?php
        $tag = $row['tag'];        // fx "#php"
        $count = $row['count'];    // fx 2
        $clean = urlencode(ltrim($tag, '#'));
    ?>
    <div class="trending-item">
        <div class="trending-info">
            <span class="item_title">
                Trending · <?= $count ?> posts
            </span>

            <p>
                <a class="hashtag-link" href="/hashtag/<?= $clean ?>">
                    <?= htmlspecialchars($tag) ?>
                </a>
            </p>
        </div>

        <span class="option">⋮</span>
    </div>
<?php endforeach; ?>
</div>