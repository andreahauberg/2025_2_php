<article class="post">
    <a href="/user?user_pk=<?php _($post["post_user_fk"]); ?>">
    <img src="https://avatar.iran.liara.run/public/101" alt="Profile Picture" class="avatar">
    <div class="post-content">
        <div class="post-header">
            <span class="name"><?php _($post["user_full_name"]); ?></span>
            <span class="handle"><?php _($post["user_username"]); ?></span> · <span class="time">1d</span>
        </div>
        <p class="text">
            <?php _($post["post_message"]); ?>
        </p>
        <?php if (!empty($post["post_image_path"])): ?>
            <img src="<?php _($post["post_image_path"]); ?>" alt="Post image" class="post-image">
        <?php endif; ?>
        <div class="post-actions">
            <span class="action comment-btn" data-post-pk="<?php _($post["post_pk"]); ?>" data-user-pk="<?php echo $_SESSION["user"]["user_pk"]; ?>">
                <i id="comment_<?php _($post["post_pk"]); ?>" class="fa-regular fa-comment"></i>
                <span class="comment-count"><?php echo $post['comment_count'] ?? 0; ?></span>
            </span>
            <span class="action"><i id="retweet_<?php _($post["post_pk"]); ?>" class="fa-solid fa-retweet"></i> 5</span>
            <span class="action flip-btn" data-post-pk="<?php _($post["post_pk"]); ?>">
                <i id="like_<?php _($post["post_pk"]); ?>" class="<?php echo $post['is_liked_by_user'] ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                <span class="like-count"><?php echo $post['like_count'] ?? 0; ?></span>
            </span>
            <?php if ($post["post_user_fk"] === $_SESSION["user"]["user_pk"]): ?>
                <span class="action">
                    <i class="fa-solid fa-ellipsis update-post-btn" data-post-pk="<?php _($post["post_pk"]); ?>" data-open="updatePostDialog"></i>
                </span>
            <?php endif; ?>
        </div>
        </a>
        <!-- Kommentar-dialog (skjult som standard) -->
        <div class="comment-dialog" id="commentDialog_<?php _($post["post_pk"]); ?>" style="display: none;">
            <div class="comments-container" id="commentsContainer_<?php _($post["post_pk"]); ?>"></div>
            <form class="comment-form" data-post-pk="<?php _($post["post_pk"]); ?>">
                <textarea name="comment_message" placeholder="Write a comment..." required></textarea>
                <button class="comment-form_btn" type="submit">Comment</button>
            </form>
        </div>
    </div>
</article>
