<?php

class CommentModel
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/DBModel.php';
        $conn = new DBModel();
        $this->db = $conn->getDB();
    }

    /**
     * Count non-deleted comments for a post
     * @param string $postPk
     * @return int
     */
    public function countForPost(string $postPk): int
    {
        $sql = 'SELECT COUNT(*) FROM comments WHERE comment_post_fk = :post_pk AND deleted_at IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':post_pk', $postPk);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Fetch comments for a post (simple helper) — returns array of rows
     * @param string $postPk
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getByPost(string $postPk, ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT c.*, u.user_full_name, u.user_handle
            FROM comments c
            LEFT JOIN users u ON u.user_pk = c.comment_user_fk
            WHERE c.comment_post_fk = :post_pk AND c.deleted_at IS NULL
              AND (u.deleted_at IS NULL OR u.user_pk IS NULL)
            ORDER BY c.comment_created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        if ($offset !== null) {
            $sql .= " OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':post_pk', $postPk);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        if ($offset !== null) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
