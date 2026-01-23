<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../Model/ClipboardGroup.php';

class ClipboardGroupRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(ClipboardGroup $group): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO clipboard_groups (name, description, created_by)
            VALUES (:name, :description, :created_by)
        ");
        $stmt->execute([
            ':name' => $group->name,
            ':description' => $group->description,
            ':created_by' => $group->created_by
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?ClipboardGroup
    {
        $stmt = $this->db->prepare("SELECT * FROM clipboard_groups WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new ClipboardGroup(
            $row['name'],
            $row['created_by'],
            $row['description'],
            null,
            $row['id'],
            $row['created_at']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM clipboard_groups");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new ClipboardGroup(
            $row['name'],
            $row['created_by'],
            $row['description'],
            null,
            $row['id'],
            $row['created_at']
        ), $rows);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM clipboard_groups WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function addClipboardToGroup(int $clipboardId, int $groupId): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO clipboard_group_map (clipboard_id, group_id)
            VALUES (:clipboard_id, :group_id)
        ");
        $stmt->execute([
            ':clipboard_id' => $clipboardId,
            ':group_id' => $groupId
        ]);
    }

    public function removeClipboardFromGroup(int $clipboardId, int $groupId): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM clipboard_group_map
            WHERE clipboard_id = :clipboard_id AND group_id = :group_id
        ");
        $stmt->execute([
            ':clipboard_id' => $clipboardId,
            ':group_id' => $groupId
        ]);
    }

    public function getClipboardsByGroup(int $groupId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*
            FROM clipboards c
            JOIN clipboard_group_map m ON m.clipboard_id = c.id
            WHERE m.group_id = :group_id
        ");
        $stmt->execute([':group_id' => $groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupsByClipboard(int $clipboardId): array
    {
        $stmt = $this->db->prepare("
            SELECT g.*
            FROM clipboard_groups g
            JOIN clipboard_group_map m ON m.group_id = g.id
            WHERE m.clipboard_id = :clipboard_id
        ");
        $stmt->execute([':clipboard_id' => $clipboardId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
