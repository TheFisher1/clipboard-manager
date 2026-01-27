<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../Model/ClipboardSubscription.php';

class ClipboardSubscriptionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(int $clipboardId, int $userId, bool $emailNotifications = true): bool
    {
        $sql = "
            INSERT INTO clipboard_subscriptions 
            (clipboard_id, user_id, email_notifications)
            VALUES (:clipboard_id, :user_id, :email_notifications)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'clipboard_id' => $clipboardId,
            'user_id' => $userId,
            'email_notifications' => $emailNotifications
        ]);
    }

    public function find(int $clipboardId, int $userId): ?ClipboardSubscription
    {
        $sql = "
            SELECT * FROM clipboard_subscriptions
            WHERE clipboard_id = :clipboard_id
              AND user_id = :user_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'clipboard_id' => $clipboardId,
            'user_id' => $userId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new ClipboardSubscription(
            (int)$row['id'],
            (int)$row['clipboard_id'],
            (int)$row['user_id'],
            (bool)$row['email_notifications'],
            $row['subscribed_at']
        );
    }

    public function findByUser(int $userId): array
    {
        $sql = "
            SELECT * FROM clipboard_subscriptions
            WHERE user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $subscriptions = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = new ClipboardSubscription(
                (int)$row['id'],
                (int)$row['clipboard_id'],
                (int)$row['user_id'],
                (bool)$row['email_notifications'],
                $row['subscribed_at']
            );
        }

        return $subscriptions;
    }

    public function updateEmailNotifications(
        int $clipboardId,
        int $userId,
        bool $enabled
    ): bool {
        $sql = "
            UPDATE clipboard_subscriptions
            SET email_notifications = :email_notifications
            WHERE clipboard_id = :clipboard_id
              AND user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'email_notifications' => $enabled,
            'clipboard_id' => $clipboardId,
            'user_id' => $userId
        ]);
    }

    public function delete(int $clipboardId, int $userId): bool
    {
        $sql = "
            DELETE FROM clipboard_subscriptions
            WHERE clipboard_id = :clipboard_id
              AND user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'clipboard_id' => $clipboardId,
            'user_id' => $userId
        ]);
    }
}
