<article class="post">
    <img src="https://avatar.iran.liara.run/public/101" alt="Profile Picture" class="avatar">
    <div class="post-content">
        <div class="post-header">
            <span class="name"><?php _($post["user_full_name"])?></span>
            <span class="handle"><?php _($post["user_username"])?></span> · <span class="time">1d</span>
        </div>
        <p class="text">
            <?php _($post["post_message"]);?>
        </p>
        <img src="<?php  _($post["post_image_path"]); ?>" alt="Post image" class="post-image">
        <div class="post-actions">
            <span class="action"><i id="comment_<?php _($post["post_pk"]) ?>" class="fa-regular fa-comment"></i> 12</span>
            <span class="action"><i id="retweet_<?php _($post["post_pk"]) ?>" class="fa-solid fa-retweet"></i> 5</span>
            <span class="action flip-btn"><i id="like_<?php _($post["post_pk"]) ?>" class="fa-regular fa-heart"></i> 87</span>
            <span class="action"><i id="stats_<?php _($post["post_pk"]) ?>" class="fa-solid fa-chart-simple"></i> 230</span>
            <span class="action"><i id="share_<?php _($post["post_pk"]) ?>" class="fa-solid fa-share"></i></span>
        </div>
    </div>
</article>