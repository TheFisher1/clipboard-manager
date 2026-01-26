<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardActivityRepository.php';
require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
require_once __DIR__ . '/../../Core/Repository/ClipboardItemRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardActivity.php';

class ClipboardActivityController
{
    private ClipboardActivityRepository $repository;
    private ClipboardRepository $clipboardRepository;
    private ClipboardItemRepository $clipboardItemRepository;


    public function __construct()
    {
        $this->repository = new ClipboardActivityRepository();
        $this->clipboardRepository = new ClipboardRepository();
        $this->clipboardItemRepository = new ClipboardItemRepository();

    }

    public function handleRequest(string $method, ?string $type, ?string $id, int $userId): void
    {
        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($type, $id, $userId);
                    break;

                case 'POST':
                    $this->create($userId);
                    break;

                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function handleGet(?string $type, ?string $id, int $userId): void
    {
        if ($type === null) {
            $this->getAll();
            return;
        }

        switch ($type) {
            case 'user':
                $this->getByUser($userId);
                break;

            case 'clipboard':
                $this->getByClipboard((int)$id, $userId);
                break;

            case 'item':
                $this->getByItem((int)$id, $userId);
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
    private function getByClipboard(int $clipboardId, int $userId): void
    {
        $clipboard = $this->clipboardRepository->findById($clipboardId);

        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if ($clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot access activities of clipboard thats not yours', 403);
            return;
        }

        $activities = $this->repository->findByClipboard($clipboardId);
        $this->sendResponse(array_map(fn($a) => $this->toArray($a), $activities));
    }

    private function getByItem(int $itemId, int $userId): void
    {
        $item = $this->clipboardItemRepository->findById($itemId);

        if (!$item) {
            $this->sendError('Item not found', 404);
            return;
        }

        if ($item->getSubmittedBy() !== $userId) {
            $this->sendError('You cannot access activities of item thats not yours', 403);
            return;
        }

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

    private function create(int $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['clipboard_id'], $data['action_type'])) {
            $this->sendError('Missing required fields: clipboard_id, action_type', 400);
            return;
        }

        $activity = new ClipboardActivity(
            (int)$data['clipboard_id'],
            $userId,
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
