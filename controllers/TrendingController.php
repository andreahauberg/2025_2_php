<?php
require_once __DIR__ . '/../db.php';

class TrendingController {

    public function getTrending($limit = 4, $offset = 0) {
        global $_db;
    
        $sql = "
            SELECT 
                LEFT(post_message, 40) AS topic,
                COUNT(*) AS post_count
            FROM posts
            GROUP BY topic
            ORDER BY post_count DESC
            LIMIT :limit OFFSET :offset
        ";
    
        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    }
