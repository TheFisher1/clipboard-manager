<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../Model/Clipboard.php';


class ClipboardRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create(Clipboard $clipboard): int
    {
        $sql = "
            INSERT INTO clipboards (
                name,
                description,
                owner_id,
                is_public,
                max_subscribers,
                max_items,
                allowed_content_types,
                default_expiration_minutes
            ) VALUES (
                :name,
                :description,
                :owner_id,
                :is_public,
                :max_subscribers,
                :max_items,
                :allowed_content_types,
                :default_expiration_minutes
            )
        ";

        $stmt = $this->db->prepare($sql);
        $data = $clipboard->toDatabaseArray();

        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':owner_id' => $data['owner_id'],
            ':is_public' => $data['is_public'],
            ':max_subscribers' => $data['max_subscribers'],
            ':max_items' => $data['max_items'],
            ':allowed_content_types' => $data['allowed_content_types'],
            ':default_expiration_minutes' => $data['default_expiration_minutes']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?Clipboard
    {
        $stmt = $this->db->prepare("SELECT * FROM clipboards WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Clipboard::fromDatabase($row) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM clipboards");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Clipboard::fromDatabase($row), $rows);
    }

    public function update(Clipboard $clipboard): bool
    {
        if ($clipboard->getId() === null) {
            throw new Exception("Clipboard ID is required for update.");
        }

        $sql = "
            UPDATE clipboards SET
                name = :name,
                description = :description,
                is_public = :is_public,
                max_subscribers = :max_subscribers,
                max_items = :max_items,
                allowed_content_types = :allowed_content_types,
                default_expiration_minutes = :default_expiration_minutes
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $data = $clipboard->toDatabaseArray();

        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':is_public' => $data['is_public'],
            ':max_subscribers' => $data['max_subscribers'],
            ':max_items' => $data['max_items'],
            ':allowed_content_types' => $data['allowed_content_types'],
            ':default_expiration_minutes' => $data['default_expiration_minutes'],
            ':id' => $clipboard->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clipboards WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function findByOwnerId(int $ownerId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM clipboards WHERE owner_id = :owner_id");
        $stmt->execute([':owner_id' => $ownerId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => Clipboard::fromDatabase($row), $rows);
    }

    public function findPublic(): array
    {
        $stmt = $this->db->query("SELECT * FROM clipboards WHERE is_public = 1");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Clipboard::fromDatabase($row), $rows);
    }
    public function findPublicOrOwned(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM clipboards 
            WHERE is_public = 1 OR owner_id = :userId
        ");
        
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Clipboard::class);
    }

    public function deleteExpired(): int
    {
        $stmt = $this->db->prepare("
            DELETE FROM clipboards
            WHERE default_expiration_minutes IS NOT NULL
            AND DATE_ADD(created_at, INTERVAL default_expiration_minutes MINUTE) <= NOW()
        ");

        $stmt->execute();
        return $stmt->rowCount();
    }

}
