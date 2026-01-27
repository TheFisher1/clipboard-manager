<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardSubscriptionRepository.php';
require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardSubscription.php';

class ClipboardSubscriptionController
{
    private ClipboardSubscriptionRepository $repository;
    private ClipboardRepository $clipboardRepository;

    public function __construct()
    {
        $this->repository = new ClipboardSubscriptionRepository();
        $this->clipboardRepository = new ClipboardRepository();
    }

    public function handleRequest(string $method, ?string $clipboardId, int $userId): void
    {
        try {
            switch ($method) {
                case 'GET':
                    if ($clipboardId) {
                        $this->getOne((int)$clipboardId, $userId);
                    } else {
                        $this->getByUser($userId);
                    }
                    break;

                case 'POST':
                    $this->create($userId);
                    break;

                case 'PUT':
                    if (!$clipboardId) {
                        $this->sendError('Missing parameters', 400);
                        return;
                    }
                    $this->update((int)$clipboardId, $userId);
                    break;

                case 'DELETE':
                    if (!$clipboardId) {
                        $this->sendError('Missing parameters', 400);
                        return;
                    }
                    $this->delete((int)$clipboardId, $userId);
                    break;

                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function getOne(int $clipboardId, int $userId): void
    {
        $subscription = $this->repository->find($clipboardId, $userId);

        if (!$subscription) {
            $this->sendError('Subscription not found', 404);
            return;
        }

        $this->sendResponse($this->toArray($subscription));
    }

    private function getByUser(int $userId): void
    {
        $subscriptions = $this->repository->findByUser($userId);

        $this->sendResponse(
            array_map(fn($s) => $this->toArray($s), $subscriptions)
        );
    }

    private function create(int $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['clipboard_id'])) {
            $this->sendError('Missing required fields: clipboard_id', 400);
            return;
        }

        $clipboardId = (int)$data['clipboard_id'];
        $clipboard = $this->clipboardRepository->findById($clipboardId);

        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }
    
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot subscribe to a private clipboard', 403);
            return;
        }

        $created = $this->repository->create(
            (int)$data['clipboard_id'],
            $userId,
            $data['email_notifications'] ?? true
        );

        if (!$created) {
            $this->sendError('Subscription already exists', 409);
            return;
        }

        $subscription = $this->repository->find(
            (int)$data['clipboard_id'],
            $userId
        );

        $this->sendResponse($this->toArray($subscription), 201);
    }

    private function update(int $clipboardId, int $userId): void
    {
        $subscription = $this->repository->find($clipboardId, $userId);

        if (!$subscription) {
            $this->sendError('Subscription not found', 404);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email_notifications'])) {
            $this->sendError('Missing field: email_notifications', 400);
            return;
        }

        $this->repository->updateEmailNotifications(
            $clipboardId,
            $userId,
            (bool)$data['email_notifications']
        );

        $updated = $this->repository->find($clipboardId, $userId);

        $this->sendResponse($this->toArray($updated));
    }

    private function delete(int $clipboardId, int $userId): void
    {
        $subscription = $this->repository->find($clipboardId, $userId);

        if (!$subscription) {
            $this->sendError('Subscription not found', 404);
            return;
        }

        $this->repository->delete($clipboardId, $userId);

        $this->sendResponse(['message' => 'Unsubscribed successfully']);
    }

    private function toArray(ClipboardSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'clipboard_id' => $subscription->clipboard_id,
            'user_id' => $subscription->user_id,
            'email_notifications' => $subscription->email_notifications,
            'subscribed_at' => $subscription->subscribed_at
        ];
    }

    private function sendResponse($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    private function sendError(string $message, int $code = 400): void
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
