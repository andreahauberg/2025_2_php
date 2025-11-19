<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h1>
        <?php
        require_once __DIR__.'/../controllers/UserController.php';
        //instantiate the controller
        $userController = new UserController();
        $user = $userController->getUser();
        // print_r($user);
        echo $user['user_full_name'];
        ?>
    </h1>
    <h2>
        <?php
        // echo $userController->getUsername();

        ?>
    </h2>
    
</body>
</html>