<?php
require_once __DIR__ . '/../db.php';

class NotificationModel {

    public function createForFollowers(string $actorPk, string $postPk, string $message) {
        global $_db;

        // find followers: users who follow the actor
        $q = "SELECT follower_user_fk FROM follows WHERE follow_user_fk = :actor";
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
                $notifPk = bin2hex(random_bytes(50)); // 100 hex chars -> varchar(100)
                // execute with values array to avoid any leftover bindings
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

        $q = "SELECT COUNT(*) FROM notifications WHERE notification_user_fk = :user AND is_read = 0";
        $stmt = $_db->prepare($q);
        $stmt->bindValue(':user', $userPk);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return (int) $count;
    }
}
