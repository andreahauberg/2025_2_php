<div class="profile-info" id="<?php _($user["user_pk"]); ?>">
    <img src="https://avatar.iran.liara.run/public/94" alt="Profile Picture">
    <div class="info-copy">
        <p class="name"><?php _($user["user_full_name"]); ?></p>
        <p class="handle"><?php _("@" . $user["user_username"]); ?></p>
    </div>

    <?php 
    $user_pk = $user["user_pk"];
    require __DIR__.'/___button_follow.php';
    
    ?>
</div>