<?php

$user_pk = $_GET['user-pk'] ?? '';

echo "<mixhtml mix-replace='.button-$user_pk'>";
//require echos right away
    require_once __DIR__ . '/../components/___button_unfollow.php';
echo "</mixhtml>";