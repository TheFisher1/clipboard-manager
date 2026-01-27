<?php

class ClipboardSubscription
{
    public ?int $id;
    public int $clipboard_id;
    public int $user_id;
    public bool $email_notifications;
    public string $subscribed_at;

    public function __construct(
        ?int $id,
        int $clipboard_id,
        int $user_id,
        bool $email_notifications = true,
        string $subscribed_at = ''
    ) {
        $this->id = $id;
        $this->clipboard_id = $clipboard_id;
        $this->user_id = $user_id;
        $this->email_notifications = $email_notifications;
        $this->subscribed_at = $subscribed_at;
    }
}
