<?php
$one = $_GET["one"];
$two = $_GET["two"];
$three = $_GET["three"];
?>

searching for <?php echo "$one $two $three"?>


<form action="bridge-search" method="POST">
    <input type="text" name="user_name">
    <button>Search</button>
</form>