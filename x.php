<?php

function _($text) {
    echo htmlspecialchars($text);
}

function _noCache(){
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Clear-Site-Data: "cache", "cookies", "storage", "executionContexts"');
}

define("postMinLength", 1);
define("postMaxLength", 300);
function _validatePost() {
    $postMessage = trim($_POST['post_message']);
    $len = strlen($postMessage);

    if ($len < postMinLength || $len > postMaxLength) {
        throw new Exception("Message must be between " . postMinLength . " and " . postMaxLength . " characters");
    }
    return $postMessage;
}

define("emailMin", 6);
define("emailMax", 50);
function _validateEmail(){

    $userEmail = $_POST["user_email"];
    if(strlen($userEmail) < emailMin){
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['toast'] = ['message' => "Email must be at least ".emailMin." characters long", 'type' => 'error'];
        throw new Exception("Email must be at least ".emailMin." characters long", 400);
    }
    if(strlen($userEmail) > emailMax){
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['toast'] = ['message' => "Email must be max ".emailMax." characters long", 'type' => 'error'];
        throw new Exception("Email must be max ".emailMax." characters long", 400);
    }
    return $userEmail;

}


define("passwordMin", 6);
define("passwordMax", 50);
function _validatePassword(){

    $userPassword = trim($_POST["user_password"]);
    if(strlen($userPassword) < passwordMin){
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['toast'] = ['message' => "Password must be at least ".passwordMin." characters long", 'type' => 'error'];
        throw new Exception("Password must be at least ".passwordMin." characters long", 400);
    }
    if(strlen($userPassword) > passwordMax){
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['toast'] = ['message' => "Password must be max ".passwordMax." characters long", 'type' => 'error'];
        throw new Exception("Password must be max ".passwordMax." characters long", 400);
    }
    return $userPassword;


}

define("userFullNameMin", 2);
define("userFullNameMax", 50);
function _validateUserFullName(){
    $userFullName = trim($_POST["user_full_name"]);
    if(strlen($userFullName) < userFullNameMin){
        throw new Exception("Full name must be at least ".userFullNameMin." characters long", 400);
    }
    if(strlen($userFullName) > userFullNameMax){
        throw new Exception("Full name must be max ".userFullNameMax." characters long", 400);
    }
    return $userFullName;
}

define("usernameMin", 2);
define("usernameMax", 50);
function _validateUsername(){
    $username = trim($_POST["user_username"]);
    if(strlen($username) < usernameMin){
        throw new Exception("Username must be at least ".usernameMin." characters long", 400);
    }
    if(strlen($username) > usernameMax){
        throw new Exception("Username must be max ".usernameMax." characters long", 400);
    }
    return $username;
}


define("pkMinLength", 1);
define("pkMaxLength", 50);
function _validatePk($fieldName) {
    $pk = trim($_POST[$fieldName]);
    $len = strlen($pk);
    if ($len < pkMinLength) {
        throw new Exception("Primary key must be at least " . pkMinLength . " characters");
    } else if ($len > pkMaxLength) {
        throw new Exception("Primary key must be at most " . pkMaxLength . " characters");
    }
    return $pk;
}