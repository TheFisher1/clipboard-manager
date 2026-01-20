<?php

class ClipboardItem
{
    private ?int $id;
    private int $clipboardId;
    private string $contentType;
    private ?string $contentText;
    private ?string $filePath;
    private ?string $originalFilename;
    private ?int $fileSize;
    private ?string $url;
    private ?string $title;
    private ?string $description;
    private int $submittedBy;
    private ?string $expiresAt;
    private int $viewCount;
    private int $downloadCount;
    private bool $isSingleUse;
    private bool $isConsumed;
    private ?string $createdAt;

    public function __construct(
        int $clipboardId,
        string $contentType,
        int $submittedBy,
        ?string $contentText = null,
        ?string $filePath = null,
        ?string $originalFilename = null,
        ?int $fileSize = null,
        ?string $url = null,
        ?string $title = null,
        ?string $description = null,
        ?string $expiresAt = null,
        bool $isSingleUse = false
    ) {
        $this->id = null;
        $this->clipboardId = $clipboardId;
        $this->contentType = $contentType;
        $this->contentText = $contentText;
        $this->filePath = $filePath;
        $this->originalFilename = $originalFilename;
        $this->fileSize = $fileSize;
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
        $this->submittedBy = $submittedBy;
        $this->expiresAt = $expiresAt;
        $this->viewCount = 0;
        $this->downloadCount = 0;
        $this->isSingleUse = $isSingleUse;
        $this->isConsumed = false;
        $this->createdAt = null;
    }

    /* =======================
       Getters
       ======================= */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClipboardId(): int
    {
        return $this->clipboardId;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentText(): ?string
    {
        return $this->contentText;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSubmittedBy(): int
    {
        return $this->submittedBy;
    }

    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function getDownloadCount(): int
    {
        return $this->downloadCount;
    }

    public function isSingleUse(): bool
    {
        return $this->isSingleUse;
    }

    public function isConsumed(): bool
    {
        return $this->isConsumed;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /* =======================
       Setters
       ======================= */

    public function setContentText(?string $contentText): void
    {
        $this->contentText = $contentText;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function setOriginalFilename(?string $originalFilename): void
    {
        $this->originalFilename = $originalFilename;
    }

    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setExpiresAt(?string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function setSingleUse(bool $isSingleUse): void
    {
        $this->isSingleUse = $isSingleUse;
    }

    public function setConsumed(bool $isConsumed): void
    {
        $this->isConsumed = $isConsumed;
    }

    public function incrementViewCount(): void
    {
        $this->viewCount++;
    }

    public function incrementDownloadCount(): void
    {
        $this->downloadCount++;
    }

    /* =======================
       Helpers
       ======================= */

    public static function fromDatabase(array $row): self
    {
        $item = new self(
            (int)$row['clipboard_id'],
            $row['content_type'],
            (int)$row['submitted_by'],
            $row['content_text'] ?? null,
            $row['file_path'] ?? null,
            $row['original_filename'] ?? null,
            $row['file_size'] ?? null,
            $row['url'] ?? null,
            $row['title'] ?? null,
            $row['description'] ?? null,
            $row['expires_at'] ?? null,
            (bool)$row['is_single_use']
        );

        $item->id = (int)$row['id'];
        $item->viewCount = (int)$row['view_count'];
        $item->downloadCount = (int)$row['download_count'];
        $item->isConsumed = (bool)$row['is_consumed'];
        $item->createdAt = $row['created_at'];

        return $item;
    }

    public function toDatabaseArray(): array
    {
        return [
            'clipboard_id' => $this->clipboardId,
            'content_type' => $this->contentType,
            'content_text' => $this->contentText,
            'file_path' => $this->filePath,
            'original_filename' => $this->originalFilename,
            'file_size' => $this->fileSize,
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description,
            'submitted_by' => $this->submittedBy,
            'expires_at' => $this->expiresAt,
            'view_count' => $this->viewCount,
            'download_count' => $this->downloadCount,
            'is_single_use' => $this->isSingleUse,
            'is_consumed' => $this->isConsumed,
        ];
    }
}
