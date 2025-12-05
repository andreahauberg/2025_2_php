<?php
require_once __DIR__ . '/../db.php';

class NotificationModel {

    public function createForFollowers(string $actorPk, string $postPk, string $message) {
        global $_db;

        // find followers: users who follow the actor
        // only notify followers that still exist (not soft-deleted)
        $q = "SELECT f.follower_user_fk FROM follows f JOIN users u ON f.follower_user_fk = u.user_pk WHERE f.follow_user_fk = :actor AND u.deleted_at IS NULL";
        $stmt = $_db->prepare($q);
        $stmt->bindValue(':actor', $actorPk);
        $stmt->execute();
        // fetch single column of follower PKs
        $followers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($followers)) return 0;

        try {
            $_db->beginTransaction();

            $insert = $_db->prepare(
                "INSERT INTO notifications (notification_pk, notification_user_fk, notification_actor_fk, notification_post_fk, notification_message, is_read, created_at) VALUES (:pk, :user_fk, :actor_fk, :post_fk, :message, 0, NOW())"
            );

            $count = 0;
            foreach ($followers as $followerPk) {
                $notifPk = bin2hex(random_bytes(50)); 
                $insert->execute([
                    ':pk' => $notifPk,
                    ':user_fk' => $followerPk,
                    ':actor_fk' => $actorPk,
                    ':post_fk' => $postPk,
                    ':message' => $message
                ]);
                $count++;
            }

            $_db->commit();
            return $count;
        } catch (Exception $e) {
            try { $_db->rollBack(); } catch (Exception $_) {}
            error_log("[NotificationModel] createForFollowers error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count unread notifications for a given user PK. Returns an integer >= 0 to fix  error in nav component.
     */
    public function countUnreadForUser(string $userPk): int {
        global $_db;
        $q = "
            SELECT COUNT(*)
            FROM notifications n
            JOIN users a ON a.user_pk = n.notification_actor_fk
            WHERE n.notification_user_fk = :user
              AND n.is_read = 0
              AND (n.deleted_at IS NULL OR n.deleted_at = '0000-00-00 00:00:00')
              AND a.deleted_at IS NULL
              AND (n.notification_post_fk IS NOT NULL)
        ";

        $stmt = $_db->prepare($q);
        $stmt->bindValue(':user', $userPk);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Delete notifications related to a user (actor or recipient).
     * Returns number of deleted rows.
     */
    public function deleteForUser(string $userPk): int {
        global $_db;

        $sql = "DELETE FROM notifications WHERE notification_actor_fk = :user OR notification_user_fk = :user";
        $stmt = $_db->prepare($sql);
        $stmt->bindValue(':user', $userPk);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
