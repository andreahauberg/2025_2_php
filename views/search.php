<div class="search-results">

    <section class="search-results__section">
        <h3 class="search-results__title">Users</h3>

        <?php if (empty($users)): ?>
            <p>No users found</p>
        <?php else: ?>
            <ul class="search-results__list">
                <?php foreach ($users as $user): ?>
                    <li class="search-results__user">

                        <div>
                            <span
                                class="search-results__user-name"
                                data-search-text="<?php echo htmlspecialchars($user["user_full_name"]); ?>">
                                <?php echo htmlspecialchars($user["user_full_name"]); ?>
                            </span>

                            <span
                                class="search-results__user-handle"
                                data-search-text="@<?php echo htmlspecialchars($user["user_username"]); ?>">
                                @<?php echo htmlspecialchars($user["user_username"]); ?>
                            </span>
                        </div>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="search-results__section">
        <h3 class="search-results__title">Posts</h3>

        <?php if (empty($posts)): ?>
            <p>No posts found</p>
        <?php else: ?>
            <div class="search-results__grid">

<?php foreach ($posts as $post): ?>
    <article class="search-results__post-card">

        <?php if (!empty($post["post_image_path"])): ?>
            <div class="search-results__post-image-wrapper">
                <img src="<?php echo htmlspecialchars($post["post_image_path"]); ?>"
                     class="search-results__post-image">
            </div>
        <?php endif; ?>

        <div class="search-results__post-body">

            <div class="search-results__post-author">

<span
    data-search-text="@<?php echo htmlspecialchars($post["user_username"]); ?>">
    @<?php echo htmlspecialchars($post["user_username"]); ?>
</span>

            </div>

            <p
                class="search-results__post-text"
                data-search-text="<?php echo htmlspecialchars($post["post_message"]); ?>">
                <?php echo htmlspecialchars($post["post_message"]); ?>
            </p>

        </div>

    </article>
<?php endforeach; ?>

            </div>
        <?php endif; ?>

    </section>

</div>