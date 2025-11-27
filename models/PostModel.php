<?php
require_once __DIR__ . '/../db.php';

class PostModel {

    public function getPostsForFeed($limit = 50) {
        global $_db;

        $sql = "
            SELECT 
                p.post_pk,
                p.post_message,
                p.post_image_path,
                p.post_user_fk,
                p.created_at,
                p.updated_at,
                u.user_username,
                u.user_full_name
            FROM posts p
            JOIN users u ON u.user_pk = p.post_user_fk
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT :limit
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->attachMeta($posts);
    }


    public function getPostsByHashtag($tag) {
        global $_db;

        if (!$tag) return [];

        $escaped = preg_quote($tag, '/');
        $pattern = "(^|[^A-Za-z0-9_])#$escaped([^A-Za-z0-9_]|$)";

        $sql = "
            SELECT 
                p.post_pk,
                p.post_message,
                p.post_image_path,
                p.post_user_fk,
                p.created_at,
                p.updated_at,
                u.user_username,
                u.user_full_name
            FROM posts p
            JOIN users u ON u.user_pk = p.post_user_fk
            WHERE p.deleted_at IS NULL
              AND p.post_message REGEXP :pattern
            ORDER BY p.created_at DESC
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':pattern', $pattern);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->attachMeta($posts);
    }


    public function getPostById($postId) {
        global $_db;

        $sql = "
            SELECT 
                p.post_pk,
                p.post_message,
                p.post_image_path,
                p.post_user_fk,
                p.created_at,
                p.updated_at,
                u.user_username,
                u.user_full_name
            FROM posts p
            JOIN users u ON u.user_pk = p.post_user_fk
            WHERE p.deleted_at IS NULL
              AND p.post_pk = :id
            LIMIT 1
        ";

        $stmt = $_db->prepare($sql);
        $stmt->bindValue(":id", $postId);
        $stmt->execute();

        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$post) return null;

        return $this->attachMeta([$post])[0];
    }


    private function attachMeta($posts) {
        global $_db;
        if (!$posts) return [];

        foreach ($posts as &$post) {

            // comment count
            $stmt = $_db->prepare(
                "SELECT COUNT(*) FROM comments WHERE comment_post_fk = :id"
            );
            $stmt->bindValue(":id", $post["post_pk"]);
            $stmt->execute();
            $post["comment_count"] = $stmt->fetchColumn();

            // like count
            $stmt = $_db->prepare(
                "SELECT COUNT(*) FROM likes WHERE like_post_fk = :id"
            );
            $stmt->bindValue(":id", $post["post_pk"]);
            $stmt->execute();
            $post["like_count"] = $stmt->fetchColumn();

            // liked by current user?
            $userId = $_SESSION["user"]["user_pk"] ?? null;

            if ($userId) {
                $stmt = $_db->prepare("
                    SELECT COUNT(*) 
                    FROM likes 
                    WHERE like_post_fk = :post AND like_user_fk = :user
                ");
                $stmt->bindValue(":post", $post["post_pk"]);
                $stmt->bindValue(":user", $userId);
                $stmt->execute();
                $post["is_liked_by_user"] = $stmt->fetchColumn() > 0;
            } else {
                $post["is_liked_by_user"] = false;
            }
        }

        return $posts;
    }
}