<?php
require_once __DIR__ . '/../db.php';

class SearchModel {

    public function searchUsers($term) {
        global $_db;

        $sql = "
            SELECT 
                user_pk,
                user_username,
                user_full_name,
                user_email
            FROM users
            WHERE 
                user_username LIKE :q
                OR user_full_name LIKE :q
                OR user_email LIKE :q
            LIMIT 25
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':q', "%$term%");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchPosts($term) {
        global $_db;

        $sql = "
            SELECT 
                posts.post_pk,
                posts.post_message,
                posts.post_image_path,
                posts.post_user_fk,
                users.user_username,
                users.user_full_name
            FROM posts
            JOIN users ON posts.post_user_fk = users.user_pk
            WHERE posts.post_message LIKE :q
            LIMIT 25
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':q', "%$term%");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Hashtag søgning til trending clicks
    public function searchHashtag($tag) {
        global $_db;

        $sql = "
            SELECT 
                posts.post_pk,
                posts.post_message,
                posts.post_image_path,
                posts.post_user_fk,
                users.user_username,
                users.user_full_name
            FROM posts
            JOIN users ON posts.post_user_fk = users.user_pk
            WHERE posts.post_message REGEXP :tag
            LIMIT 50
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':tag', "#$tag");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}