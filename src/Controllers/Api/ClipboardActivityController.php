<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardActivityRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardActivity.php';

class ClipboardActivityController
{
    private ClipboardActivityRepository $repository;

    public function __construct()
    {
        $this->repository = new ClipboardActivityRepository();
    }

    public function handleRequest(string $method, ?string $type, ?string $id): void
    {
        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($type, $id);
                    break;

                case 'POST':
                    $this->create();
                    break;

                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function handleGet(?string $type, ?string $id): void
    {
        if ($type === null) {
            $this->getAll();
            return;
        }

        switch ($type) {
            case 'user':
                $this->getByUser((int)$id);
                break;

            case 'clipboard':
                $this->getByClipboard((int)$id);
                break;

            case 'item':
                $this->getByItem((int)$id);
                break;

            case 'id':
                $this->getById((int)$id);
                break;

            default:
                $this->sendError('Invalid endpoint', 400);
        }
    }

    private function getAll(): void
    {
        $activities = $this->repository->findAll();
        $this->sendResponse(array_map(fn($a) => $this->toArray($a), $activities));
    }

    private function getByUser(int $userId): void
    {
        $activities = $this->repository->findByUser($userId);
        $this->sendResponse(array_map(fn($a) => $this->toArray($a), $activities));
    }

    private function getByClipboard(int $clipboardId): void
    {
        $activities = $this->repository->findByClipboard($clipboardId);
        $this->sendResponse(array_map(fn($a) => $this->toArray($a), $activities));
    }

    private function getByItem(int $itemId): void
    {
        $activities = $this->repository->findByItem($itemId);
        $this->sendResponse(array_map(fn($a) => $this->toArray($a), $activities));
    }

    private function getById(int $id): void
    {
        $activity = $this->repository->findById($id);

        if (!$activity) {
            $this->sendError('Activity not found', 404);
            return;
        }

        $this->sendResponse($this->toArray($activity));
    }

    private function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['clipboard_id'], $data['user_id'], $data['action_type'])) {
            $this->sendError('Missing required fields: clipboard_id, user_id, action_type', 400);
            return;
        }

        $activity = new ClipboardActivity(
            (int)$data['clipboard_id'],
            (int)$data['user_id'],
            $data['action_type'],
            $data['item_id'] ?? null,
            $data['details'] ?? null,
            $data['ip_address'] ?? null,
            $data['user_agent'] ?? null
        );

        $id = $this->repository->create($activity);
        $created = $this->repository->findById($id);

        $this->sendResponse($this->toArray($created), 201);
    }

    private function toArray(ClipboardActivity $activity): array
    {
        return [
            'id' => $activity->getId(),
            'clipboard_id' => $activity->getClipboardId(),
            'item_id' => $activity->getItemId(),
            'user_id' => $activity->getUserId(),
            'action_type' => $activity->getActionType(),
            'details' => $activity->getDetails(),
            'ip_address' => $activity->getIpAddress(),
            'user_agent' => $activity->getUserAgent(),
            'created_at' => $activity->getCreatedAt(),
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
