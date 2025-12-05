<?php
// ------------------------------------------------------------
// DATABASE CONNECTION
// ------------------------------------------------------------
$db = new PDO("mysql:host=mariadb;dbname=company;charset=utf8mb4", "root", "password");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ------------------------------------------------------------
// HELPERS
// ------------------------------------------------------------
function rand50() {
    return bin2hex(random_bytes(25)); 
}
// user_is_verified = 1
// user_verify_token = NULL
function fakeMessage() {
    $emojis = ["🔥","✨","😊","🎉","📸","💬","🙌","👍","💡","📍","⭐","😎","🛠️","❤️","🚀","📢"];
    $hashtags = ["#update","#news","#hello","#random","#coding","#project","#daily","#vibes","#now","#trending","#life","#post"];
    $words = [
        "new","post","today","great","check","feature","system",
        "working","live","testing","nice","message","social",
        "feed","example","update","amazing","cool","fresh",
        "nice","happening","random","latest"
    ];

    shuffle($words);
    shuffle($emojis);
    shuffle($hashtags);

    return ucfirst(
        implode(" ", array_slice($words, 0, rand(3, 10))) .
        " " . $emojis[array_rand($emojis)] .
        " " . $hashtags[array_rand($hashtags)]
    );
}

function fakeUser() {
    $first = ["Andrea","Sofie","Mikkel","Oliver","Freja","Ida","William","Lucas","Emilie","Noah","Luna","Alma","Elias","Clara","August","Storm","Theo","Liv","Sara","Anna"];
    $last  = ["Hansen","Nielsen","Larsen","Jensen","Poulsen","Christensen","Møller","Mortensen","Frandsen","Knudsen","Bach","Bendtsen","Dreyer","Mathiasen","Holm"];

    $f = $first[array_rand($first)];
    $l = $last[array_rand($last)];
    $full = "$f $l";

    $username = strtolower($f . "_" . $l . rand(10,99));
    $email = $username . "@example.com";

    return [
        "full" => $full,
        "username" => $username,
        "email" => $email
    ];
}

// ------------------------------------------------------------
// 1. SEED USERS — ***KRITISK***
// ------------------------------------------------------------
$users = [];

for ($i = 0; $i < 20; $i++) {
    $pk = rand50();
    $u = fakeUser();
    $password = password_hash("test1234", PASSWORD_DEFAULT);

    $db->prepare("
        INSERT INTO users (user_pk, user_username, user_email, user_password, user_full_name, created_at)
        VALUES (:pk, :un, :em, :pw, :fn, NOW())
    ")->execute([
        ":pk" => $pk,
        ":un" => $u["username"],
        ":em" => $u["email"],
        ":pw" => $password,
        ":fn" => $u["full"]
    ]);

    // VIGTIGT: gem alle user PKs i array
    $users[] = $pk;
}

echo "Users created: " . count($users) . "<br>";


// ------------------------------------------------------------
// 2. SEED POSTS
// ------------------------------------------------------------
$posts = [];

foreach ($users as $userPk) {
    $num = rand(1, 5);

    for ($i = 0; $i < $num; $i++) {
        $postPk = rand50();

        $img = rand(0, 3) === 1
            ? "https://picsum.photos/600/" . rand(200,350)
            : "";

        $db->prepare("
            INSERT INTO posts (post_pk, post_message, post_image_path, post_user_fk, created_at)
            VALUES (:pk, :msg, :img, :ufk, NOW())
        ")->execute([
            ":pk" => $postPk,
            ":msg" => fakeMessage(),
            ":img" => $img,
            ":ufk" => $userPk
        ]);

        $posts[] = $postPk;
    }
}

echo "Posts created: " . count($posts) . "<br>";


// ------------------------------------------------------------
// 3. SEED FOLLOWS
// ------------------------------------------------------------
foreach ($users as $u1) {
    foreach ($users as $u2) {
        if ($u1 === $u2) continue;
        if (rand(0, 4) === 1) {
            $db->prepare("
                INSERT IGNORE INTO follows (follower_user_fk, follow_user_fk)
                VALUES (?, ?)
            ")->execute([$u1, $u2]);
        }
    }
}

echo "Follows created<br>";


// ------------------------------------------------------------
// 4. SEED LIKES
// ------------------------------------------------------------
foreach ($users as $u) {
    foreach ($posts as $p) {
        if (rand(0, 5) === 1) {
            $db->prepare("
                INSERT IGNORE INTO likes (like_user_fk, like_post_fk)
                VALUES (?, ?)
            ")->execute([$u, $p]);
        }
    }
}

echo "Likes created<br>";


// ------------------------------------------------------------
// 5. SEED COMMENTS
// ------------------------------------------------------------
foreach ($posts as $postPk) {
    $count = rand(0, 5);

    for ($i = 0; $i < $count; $i++) {
        $commentPk = rand50();
        $user = $users[array_rand($users)];

        $db->prepare("
            INSERT INTO comments
            (comment_pk, comment_post_fk, comment_user_fk, comment_message, comment_created_at)
            VALUES (:pk, :pfk, :ufk, :msg, NOW())
        ")->execute([
            ":pk" => $commentPk,
            ":pfk" => $postPk,
            ":ufk" => $user,
            ":msg" => fakeMessage()
        ]);
    }
}

echo "Comments created<br>";


// ------------------------------------------------------------
// 6. SEED NOTIFICATIONS
// ------------------------------------------------------------

// FOLLOW notifications
$follows = $db->query("SELECT * FROM follows")->fetchAll();

foreach ($follows as $f) {
    $db->prepare("
        INSERT INTO notifications
        (notification_pk, notification_user_fk, notification_actor_fk, notification_message, is_read, created_at)
        VALUES (?, ?, ?, 'started following you', 0, NOW())
    ")->execute([rand50(), $f["follow_user_fk"], $f["follower_user_fk"]]);
}

// LIKE notifications
$likes = $db->query("
    SELECT likes.*, posts.post_user_fk 
    FROM likes 
    JOIN posts ON likes.like_post_fk = posts.post_pk
")->fetchAll();

foreach ($likes as $l) {
    if ($l["post_user_fk"] === $l["like_user_fk"]) continue;

    $db->prepare("
        INSERT INTO notifications
        (notification_pk, notification_user_fk, notification_actor_fk, notification_post_fk, notification_message, is_read, created_at)
        VALUES (?, ?, ?, ?, 'liked your post', 0, NOW())
    ")->execute([rand50(), $l["post_user_fk"], $l["like_user_fk"], $l["like_post_fk"]]);
}

// COMMENT notifications
$comments = $db->query("
    SELECT comments.*, posts.post_user_fk
    FROM comments
    JOIN posts ON comments.comment_post_fk = posts.post_pk
")->fetchAll();

foreach ($comments as $c) {
    if ($c["post_user_fk"] === $c["comment_user_fk"]) continue;

    $db->prepare("
        INSERT INTO notifications
        (notification_pk, notification_user_fk, notification_actor_fk, notification_post_fk, notification_message, is_read, created_at)
        VALUES (?, ?, ?, ?, 'commented on your post', 0, NOW())
    ")->execute([rand50(), $c["post_user_fk"], $c["comment_user_fk"], $c["comment_post_fk"]]);
}

echo "Notifications created<br><br>";
echo "<hr>SEED COMPLETE<br>";