<?php
session_start();
require_once __DIR__ . '/../x.php';
require_once __DIR__ . '/../db.php';

try {
    $userFullName = _validateUserFullName();
    $username     = _validateUsername();
    $userEmail    = _validateEmail();
    $userPassword = _validatePassword();
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    $check = $_db->prepare("
        SELECT user_username, user_email 
        FROM users 
        WHERE user_username = :u OR user_email = :e 
        LIMIT 1
    ");
    $check->execute([':u' => $username, ':e' => $userEmail]);
    $existing = $check->fetch();

    if ($existing) {
        if (!empty($existing['user_username']) && $existing['user_username'] === $username) {
            _toastError("Username is already taken");
        } else {
            _toastError("An account already exists with this email");
        }
        $_SESSION['open_dialog'] = 'signup';
        header("Location: /");
        exit();
    }

    $userPk = bin2hex(random_bytes(25));
    $token  = bin2hex(random_bytes(32));

    $stmt = $_db->prepare("
        INSERT INTO users (
            user_pk,
            user_username,
            user_email,
            user_password,
            user_full_name,
            user_avatar,
            user_is_verified,
            user_verify_token
        ) VALUES (
            :pk, :username, :email, :password, :full, '', 0, :token
        )
    ");

    $stmt->execute([
        ':pk'       => $userPk,
        ':username' => $username,
        ':email'    => $userEmail,
        ':password' => $hashedPassword,
        ':full'     => $userFullName,
        ':token'    => $token
    ]);

    if (!weaveIsProd()) {
        $_db->prepare("
            UPDATE users 
            SET user_is_verified = 1,
                user_verify_token = NULL
            WHERE user_pk = :pk
        ")->execute([':pk' => $userPk]);

        $fetch = $_db->prepare("SELECT * FROM users WHERE user_pk = :pk LIMIT 1");
        $fetch->execute([':pk' => $userPk]);
        $newUser = $fetch->fetch();

        if ($newUser) {
            unset($newUser['user_password']);
            $_SESSION['user'] = $newUser;
        }

        _toastOk('Welcome!');
        header("Location: /home");
        exit();
    }

    $_SESSION['last_signup_email'] = $userEmail;

    $verifyUrl = "https://michelleenoe.com/verify-email?token=$token";

    sendWeaveMail(
        $userEmail,
        "Verify your Weave account",
        "Click the link to verify your email: $verifyUrl"
    );

    _toastOk("We just sent you a verification email. Please check your inbox.");
    $_SESSION['open_dialog'] = 'signup_verified';

    header("Location: /");
    exit();

} catch (Exception $e) {
    _toastError($e->getMessage());
    $_SESSION['open_dialog'] = 'signup';

    // NEW: Preserve old input + error field
    $_SESSION['signup_old'] = $_POST;
    $_SESSION['signup_error_field'] = $e->getMessage();

    header("Location: /");
    exit();
}