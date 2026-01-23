<?php

class ClipboardGroup
{
    public ?int $id;
    public string $name;
    public ?string $description;
    public ?int $parent_group_id;
    public int $created_by;
    public string $created_at;

    public function __construct(
        string $name,
        int $createdBy,
        ?string $description = null,
        ?int $parentGroupId = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parent_group_id = $parentGroupId;
        $this->created_by = $createdBy;
        $this->created_at = $createdAt ?? date('Y-m-d H:i:s');
    }
}
