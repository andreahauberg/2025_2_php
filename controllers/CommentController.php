<?php
// controllers/PostController.php
require_once __DIR__ . '/../models/CommentModel.php';

class PostController {
    public function renderPostWithCount(array $post) {
        $commentModel = new CommentModel();
        $commentCount = $commentModel->countForPost($post['post_pk']);

        
        include __DIR__ . '/../views/partials/post.php';
    }
}