<?php

class Clipboard
{
    private ?int $id;
    private string $name;
    private ?string $description;
    private int $ownerId;
    private ?int $groupId;
    private bool $isPublic;
    private ?int $maxSubscribers;
    private ?int $maxItems;
    private ?array $allowedContentTypes; // decoded JSON
    private ?int $defaultExpirationMinutes;
    private ?string $createdAt;
    private ?string $updatedAt;

    /**
     * Constructor
     */
    public function __construct(
        string $name,
        int $ownerId,
        ?string $description = null,
        ?int $groupId = null,
        bool $isPublic = false,
        ?int $maxSubscribers = null,
        ?int $maxItems = null,
        ?array $allowedContentTypes = null,
        ?int $defaultExpirationMinutes = null
    ) {
        $this->id = null;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->description = $description;
        $this->groupId = $groupId;
        $this->isPublic = $isPublic;
        $this->maxSubscribers = $maxSubscribers;
        $this->maxItems = $maxItems;
        $this->allowedContentTypes = $allowedContentTypes;
        $this->defaultExpirationMinutes = $defaultExpirationMinutes;
        $this->createdAt = null;
        $this->updatedAt = null;
    }

    /* =======================
       Getters
       ======================= */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getMaxSubscribers(): ?int
    {
        return $this->maxSubscribers;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    public function getAllowedContentTypes(): ?array
    {
        return $this->allowedContentTypes;
    }

    public function getDefaultExpirationMinutes(): ?int
    {
        return $this->defaultExpirationMinutes;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /* =======================
       Setters
       ======================= */

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setGroupId(?int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function setPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    public function setMaxSubscribers(?int $maxSubscribers): void
    {
        $this->maxSubscribers = $maxSubscribers;
    }

    public function setMaxItems(?int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }

    public function setAllowedContentTypes(?array $allowedContentTypes): void
    {
        $this->allowedContentTypes = $allowedContentTypes;
    }

    public function setDefaultExpirationMinutes(?int $minutes): void
    {
        $this->defaultExpirationMinutes = $minutes;
    }

    /* =======================
       Helpers
       ======================= */

    /**
     * Used when loading from database
     */
    public static function fromDatabase(array $row): self
    {
        $clipboard = new self(
            $row['name'],
            (int)$row['owner_id'],
            $row['description'] ?? null,
            $row['group_id'] ?? null,
            (bool)$row['is_public'],
            $row['max_subscribers'] ?? null,
            $row['max_items'] ?? null,
            isset($row['allowed_content_types'])
                ? json_decode($row['allowed_content_types'], true)
                : null,
            $row['default_expiration_minutes'] ?? null
        );

        $clipboard->id = (int)$row['id'];
        $clipboard->createdAt = $row['created_at'];
        $clipboard->updatedAt = $row['updated_at'];

        return $clipboard;
    }

    /**
     * Convert object to array (for DB insert/update)
     */
    public function toDatabaseArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => $this->ownerId,
            'group_id' => $this->groupId,
            'is_public' => $this->isPublic,
            'max_subscribers' => $this->maxSubscribers,
            'max_items' => $this->maxItems,
            'allowed_content_types' => $this->allowedContentTypes !== null
                ? json_encode($this->allowedContentTypes)
                : null,
            'default_expiration_minutes' => $this->defaultExpirationMinutes,
        ];
    }
}

?>