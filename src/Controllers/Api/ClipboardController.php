<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
require_once __DIR__ . '/../../Core/Model/Clipboard.php';
require_once __DIR__ . '/../../Services/SessionManager.php';

class ClipboardController
{
    private ClipboardRepository $repository;

    public function __construct()
    {
        $this->repository = new ClipboardRepository();
    }

    public function handleRequest(string $method, ?string $id, ?string $subroute = null): void
    {
        try {
            switch ($method) {
                case 'GET':
                    if ($subroute === 'mine') {
                        $this->getMine();
                    } else {
                        $id ? $this->getOne((int)$id) : $this->getAll();
                    }
                    break;
                case 'POST':
                    $this->create();
                    break;
                case 'PUT':
                    $this->update((int)$id);
                    break;
                case 'DELETE':
                    $this->delete((int)$id);
                    break;
                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function getAll(): void
    {
        $userId = SessionManager::getCurrentUserId();
        $clipboards = $this->repository->findPublicOrOwned($userId);
        $this->sendResponse(array_map(fn($c) => $this->toArray($c), $clipboards));
    }

    private function getMine(): void
    {
        $userId = SessionManager::getCurrentUserId();
        $clipboards = $this->repository->findByOwnerId($userId);
        $this->sendResponse(array_map(fn($c) => $this->toArray($c), $clipboards));
    }


    private function getOne(int $id): void
    {
        $userId = SessionManager::getCurrentUserId();
        $clipboard = $this->repository->findById($id);
        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('Forbidden', 403);
            return;
        }

        $this->sendResponse($this->toArray($clipboard));
    }

    private function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['owner_id'])) {
            $this->sendError('Missing required fields: name, owner_id', 400);
            return;
        }

        $clipboard = new Clipboard(
            $data['name'],
            (int)$data['owner_id'],
            $data['description'] ?? null,
            $data['is_public'] ?? false,
            $data['max_subscribers'] ?? null,
            $data['max_items'] ?? null,
            $data['allowed_content_types'] ?? null,
            $data['default_expiration_minutes'] ?? null
        );

        $id = $this->repository->create($clipboard);
        $created = $this->repository->findById($id);
        
        $this->sendResponse($this->toArray($created), 201);
    }

    private function update(int $id): void
    {
        $userId = SessionManager::getCurrentUserId();

        $clipboard = $this->repository->findById($id);
        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if ($clipboard->getOwnerId() !== $userId) {
            $this->sendError('Forbidden', 403);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['name'])) $clipboard->setName($data['name']);
        if (isset($data['description'])) $clipboard->setDescription($data['description']);
        if (isset($data['is_public'])) $clipboard->setPublic($data['is_public']);
        if (isset($data['max_subscribers'])) $clipboard->setMaxSubscribers($data['max_subscribers']);
        if (isset($data['max_items'])) $clipboard->setMaxItems($data['max_items']);

        $this->repository->update($clipboard);
        $updated = $this->repository->findById($id);
        
        $this->sendResponse($this->toArray($updated));
    }

    private function delete(int $id): void
    {
        $userId = SessionManager::getCurrentUserId();
        $clipboard = $this->repository->findById($id);
        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if ($clipboard->getOwnerId() !== $userId) {
            $this->sendError('Forbidden', 403);
            return;
        }

        $this->repository->delete($id);
        $this->sendResponse(['message' => 'Clipboard deleted successfully']);
    }

    private function toArray(Clipboard $clipboard): array
    {
        return [
            'id' => $clipboard->getId(),
            'name' => $clipboard->getName(),
            'description' => $clipboard->getDescription(),
            'owner_id' => $clipboard->getOwnerId(),
            'is_public' => $clipboard->isPublic(),
            'max_subscribers' => $clipboard->getMaxSubscribers(),
            'max_items' => $clipboard->getMaxItems(),
            'allowed_content_types' => $clipboard->getAllowedContentTypes(),
            'default_expiration_minutes' => $clipboard->getDefaultExpirationMinutes(),
            'created_at' => $clipboard->getCreatedAt(),
            'updated_at' => $clipboard->getUpdatedAt()
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
