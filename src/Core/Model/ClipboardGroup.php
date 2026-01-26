<?php

class ClipboardGroup
{
    private ?int $id;
    private string $name;
    private ?string $description;
    private int $created_by;
    private string $created_at;

    public function __construct(
        string $name,
        int $createdBy,
        ?string $description = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setCreatedBy($createdBy);
        $this->setCreatedAt($createdAt ?? date('Y-m-d H:i:s'));
    }

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

    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->created_by = $createdBy;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->created_at = $createdAt;
    }
}
