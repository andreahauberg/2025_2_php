<?php
$__signup_state = $_SESSION['open_dialog'] ?? null;
$__signup_active = ($__signup_state === 'signup') ? ' active' : '';

$old = $_SESSION['signup_old'] ?? [];
$err = $_SESSION['signup_error_field'] ?? '';

$nameErr  = str_contains($err, 'Full name');
$userErr  = str_contains($err, 'Username');
$emailErr = str_contains($err, 'Email');
$passErr  = str_contains($err, 'Password');
?>

<?php if ($__signup_state === 'signup_verified'): ?>
<div class="x-dialog active" id="signupVerifiedDialog" role="dialog" aria-modal="true">
    <div class="x-dialog__overlay"></div>
    <div class="x-dialog__content">
        <button class="x-dialog__close" aria-label="Close">&times;</button>

        <div class="x-dialog__header">
        <img src="/public/img/weave-logo.png" alt="Weave logo" class="x-dialog__logo">
        </div>

        <div class="x-dialog__body">
            <h2>Verify your email</h2>
            <p>We’ve sent a verification link to your email address.</p>
            <p>Please check your inbox and click the link to activate your account.</p>

            <form action="/api/resend-verification.php" method="POST">
                <input type="hidden" name="email"
                    value="<?php echo htmlspecialchars($_SESSION['last_signup_email'] ?? ''); ?>">
                <button class="x-dialog__btn" style="margin-top: 1rem;">Resend verification email</button>
            </form>
        </div>
    </div>
</div>
<?php unset($_SESSION['open_dialog']); return; ?>
<?php endif; ?>

<div 
    class="x-dialog<?php echo $__signup_active; ?>" 
    id="signupDialog" 
    role="dialog" 
    aria-modal="true"
    aria-labelledby="signupTitle"
    data-open-state="<?php echo ($__signup_state === 'signup') ? 'open' : 'closed'; ?>"
    mix-ignore
    mix-on="yes"
>
    <div class="x-dialog__overlay"></div>

    <div class="x-dialog__content">
        <button class="x-dialog__close" aria-label="Close">&times;</button>

        <div class="x-dialog__header">
            <img src="/public/img/weave-logo.png" alt="Weave logo" class="x-dialog__logo">
        </div>

        <h2 id="signupTitle">Create your account</h2>

        <form class="x-dialog__form" action="bridge-signup" method="POST" autocomplete="off">

            <label for="inp_fullname">Full name</label>
            <input 
                id="inp_fullname"
                name="user_full_name"
                type="text"
                maxlength="20"
                placeholder="Enter your name"
                data-rule=".{1,20}"
                value="<?php echo htmlspecialchars($old['user_full_name'] ?? ''); ?>"
                class="<?php echo $nameErr ? 'x-error' : ''; ?>"
            >

            <label for="inp_username">Username</label>
            <input 
                id="inp_username"
                name="user_username"
                type="text"
                maxlength="20"
                placeholder="Choose a username"
                data-rule=".{1,20}"
                value="<?php echo htmlspecialchars($old['user_username'] ?? ''); ?>"
                class="<?php echo $userErr ? 'x-error' : ''; ?>"
            >

            <label for="inp_email">Email</label>
            <input 
                id="inp_email"
                name="user_email"
                type="text"
                maxlength="50"
                placeholder="Your email"
                data-rule=".{6,50}"
                value="<?php echo htmlspecialchars($old['user_email'] ?? ''); ?>"
                class="<?php echo $emailErr ? 'x-error' : ''; ?>"
            >

            <label for="inp_password">Password</label>
            <input 
                id="inp_password"
                name="user_password"
                type="password"
                maxlength="50"
                placeholder="Create a password"
                data-rule=".{6,50}"
                class="<?php echo $passErr ? 'x-error' : ''; ?>"
            >

            <button type="submit" class="x-dialog__btn">Sign up</button>
        </form>

        <p class="x-dialog__alt">Already have an account? <a href="#" data-open="loginDialog">Log in</a></p>
    </div>
</div>

<?php
unset($_SESSION['signup_old'], $_SESSION['signup_error_field'], $_SESSION['open_dialog']);
?>