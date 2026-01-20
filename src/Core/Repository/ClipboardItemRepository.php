<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../Model/ClipboardItem.php';

class ClipboardItemRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(ClipboardItem $item): int
    {
        $sql = "
            INSERT INTO clipboard_items (
                clipboard_id,
                content_type,
                content_text,
                file_path,
                original_filename,
                file_size,
                url,
                title,
                description,
                submitted_by,
                expires_at,
                is_single_use,
                is_consumed
            ) VALUES (
                :clipboard_id,
                :content_type,
                :content_text,
                :file_path,
                :original_filename,
                :file_size,
                :url,
                :title,
                :description,
                :submitted_by,
                :expires_at,
                :is_single_use,
                :is_consumed
            )
        ";

        $stmt = $this->db->prepare($sql);
        $data = $item->toDatabaseArray();

        $stmt->execute([
            ':clipboard_id' => $data['clipboard_id'],
            ':content_type' => $data['content_type'],
            ':content_text' => $data['content_text'],
            ':file_path' => $data['file_path'],
            ':original_filename' => $data['original_filename'],
            ':file_size' => $data['file_size'],
            ':url' => $data['url'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':submitted_by' => $data['submitted_by'],
            ':expires_at' => $data['expires_at'],
            ':is_single_use' => $data['is_single_use'],
            ':is_consumed' => $data['is_consumed'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?ClipboardItem
    {
        $stmt = $this->db->prepare("SELECT * FROM clipboard_items WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? ClipboardItem::fromDatabase($row) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM clipboard_items");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => ClipboardItem::fromDatabase($row), $rows);
    }

    public function findByClipboardId(int $clipboardId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM clipboard_items WHERE clipboard_id = :clipboard_id"
        );
        $stmt->execute([':clipboard_id' => $clipboardId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => ClipboardItem::fromDatabase($row), $rows);
    }

    public function update(ClipboardItem $item): bool
    {
        if ($item->getId() === null) {
            throw new Exception("ClipboardItem ID is required for update.");
        }

        $sql = "
            UPDATE clipboard_items SET
                content_type = :content_type,
                content_text = :content_text,
                file_path = :file_path,
                original_filename = :original_filename,
                file_size = :file_size,
                url = :url,
                title = :title,
                description = :description,
                expires_at = :expires_at,
                is_single_use = :is_single_use,
                is_consumed = :is_consumed
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $data = $item->toDatabaseArray();

        return $stmt->execute([
            ':content_type' => $data['content_type'],
            ':content_text' => $data['content_text'],
            ':file_path' => $data['file_path'],
            ':original_filename' => $data['original_filename'],
            ':file_size' => $data['file_size'],
            ':url' => $data['url'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':expires_at' => $data['expires_at'],
            ':is_single_use' => $data['is_single_use'],
            ':is_consumed' => $data['is_consumed'],
            ':id' => $item->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clipboard_items WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function findExpired(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM clipboard_items 
             WHERE expires_at IS NOT NULL 
             AND expires_at < NOW()"
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => ClipboardItem::fromDatabase($row), $rows);
    }

    public function incrementViewCount(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE clipboard_items SET view_count = view_count + 1 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function incrementDownloadCount(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE clipboard_items SET download_count = download_count + 1 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
