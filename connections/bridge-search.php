<?php $name = $_POST["user_name"];
$id = bin2hex(random_bytes(50));
?>

Bridge searching for ... <?php echo $name?>

<form action="delete-user" method="POST">
    <input name="user_id" type="text" value="<?php echo $id ?>">
    <button>Delete</button>
</form>