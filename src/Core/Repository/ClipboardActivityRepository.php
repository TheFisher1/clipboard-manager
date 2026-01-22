<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../Model/ClipboardActivity.php';

class ClipboardActivityRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(ClipboardActivity $activity): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO clipboard_activity (
                clipboard_id,
                item_id,
                user_id,
                action_type,
                details,
                ip_address,
                user_agent
            ) VALUES (
                :clipboard_id,
                :item_id,
                :user_id,
                :action_type,
                :details,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            ':clipboard_id' => $activity->getClipboardId(),
            ':item_id' => $activity->getItemId(),
            ':user_id' => $activity->getUserId(),
            ':action_type' => $activity->getActionType(),
            ':details' => $activity->getDetails() ? json_encode($activity->getDetails()) : null,
            ':ip_address' => $activity->getIpAddress(),
            ':user_agent' => $activity->getUserAgent(),
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?ClipboardActivity
    {
        $stmt = $this->db->prepare("SELECT * FROM clipboard_activity WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToActivity($row) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM clipboard_activity ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToActivity'], $rows);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM clipboard_activity
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToActivity'], $rows);
    }

    public function findByClipboard(int $clipboardId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM clipboard_activity
            WHERE clipboard_id = :clipboard_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':clipboard_id' => $clipboardId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToActivity'], $rows);
    }

    public function findByItem(int $itemId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM clipboard_activity
            WHERE item_id = :item_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':item_id' => $itemId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToActivity'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM clipboard_activity WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    private function mapRowToActivity(array $row): ClipboardActivity
    {
        return new ClipboardActivity(
            (int)$row['clipboard_id'],
            (int)$row['user_id'],
            $row['action_type'],
            isset($row['item_id']) ? (int)$row['item_id'] : null,
            isset($row['details']) ? json_decode($row['details'], true) : null,
            $row['ip_address'] ?? null,
            $row['user_agent'] ?? null,
            $row['created_at'],
            (int)$row['id']
        );
    }
}
