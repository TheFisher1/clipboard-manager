<?php

class ClipboardActivity
{
    private ?int $id;
    private int $clipboard_id;
    private ?int $item_id;
    private int $user_id;
    private string $action_type;
    private ?array $details;
    private ?string $ip_address;
    private ?string $user_agent;
    private string $created_at;

    public function __construct(
        int $clipboard_id,
        int $user_id,
        string $action_type,
        ?int $item_id = null,
        ?array $details = null,
        ?string $ip_address = null,
        ?string $user_agent = null,
        ?string $created_at = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->clipboard_id = $clipboard_id;
        $this->item_id = $item_id;
        $this->user_id = $user_id;
        $this->action_type = $action_type;
        $this->details = $details;
        $this->ip_address = $ip_address;
        $this->user_agent = $user_agent;
        $this->created_at = $created_at ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClipboardId(): int
    {
        return $this->clipboard_id;
    }

    public function getItemId(): ?int
    {
        return $this->item_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getActionType(): string
    {
        return $this->action_type;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setClipboardId(int $clipboard_id): void
    {
        $this->clipboard_id = $clipboard_id;
    }

    public function setItemId(?int $item_id): void
    {
        $this->item_id = $item_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setActionType(string $action_type): void
    {
        $this->action_type = $action_type;
    }

    public function setDetails(?array $details): void
    {
        $this->details = $details;
    }

    public function setIpAddress(?string $ip_address): void
    {
        $this->ip_address = $ip_address;
    }

    public function setUserAgent(?string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'clipboard_id' => $this->clipboard_id,
            'item_id' => $this->item_id,
            'user_id' => $this->user_id,
            'action_type' => $this->action_type,
            'details' => $this->details,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
        ];
    }
}
