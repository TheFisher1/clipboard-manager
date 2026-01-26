<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardGroupRepository.php';
require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardGroup.php';

class ClipboardGroupController
{
    private ClipboardGroupRepository $repository;
    private ClipboardRepository $clipboardRepository;

    public function __construct()
    {
        $this->repository = new ClipboardGroupRepository();
        $this->clipboardRepository = new ClipboardRepository();
    }

    public function handleRequest(string $method, ?string $id, int $userId, ?string $action = null, ?string $clipboardId = null): void
    {
        try {
            switch ($method) {
                case 'GET':
                    if ($id && $action === 'clipboards') {
                        $this->getClipboards((int)$id, $userId);
                    } elseif ($id) {
                        $this->getOne((int)$id, $userId);
                    } else {
                        $this->getAll();
                    }
                    break;

                case 'POST':
                    if ($id && $action === 'clipboards' && $clipboardId) {
                        $this->addClipboard((int)$id, (int)$clipboardId, $userId);
                    } else {
                        $this->create($userId);
                    }
                    break;

                case 'DELETE':
                    if ($id && $action === 'clipboards' && $clipboardId) {
                        $this->removeClipboard((int)$id, (int)$clipboardId, $userId);
                    } elseif ($id) {
                        $this->delete((int)$id, $userId);
                    } else {
                        $this->sendError('Missing group id', 400);
                    }
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
        $groups = $this->repository->findAll();
        $this->sendResponse(array_map(fn($g) => $this->toArray($g), $groups));
    }

    private function getOne(int $id, int $userId): void
    {
        $group = $this->repository->findById($id);
        if (!$group) {
            $this->sendError('Group not found', 404);
            return;
        }

        if ($group->getCreatedBy() !== $userId) {
            $this->sendError('You cannnot access group thats not yours', 403);
            return;
        }

        $this->sendResponse($this->toArray($group));
    }

    private function create(int $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            $this->sendError('Missing required fields: name', 400);
            return;
        }

        $group = new ClipboardGroup(
            $data['name'],
            $userId,
            $data['description'] ?? null
        );

        $id = $this->repository->create($group);
        $created = $this->repository->findById($id);

        $this->sendResponse($this->toArray($created), 201);
    }

    private function delete(int $id, int $userId): void
    {
        $group = $this->repository->findById($id);
        if (!$group) {
            $this->sendError('Group not found', 404);
            return;
        }

        if ($group->getCreatedBy() !== $userId) {
            $this->sendError('You cannnot delete group thats not yours', 403);
            return;
        }

        $this->repository->delete($id);
        $this->sendResponse(['message' => 'Group deleted successfully']);
    }

    private function addClipboard(int $groupId, int $clipboardId, int $userId): void
    {
        $group = $this->repository->findById($groupId);
        if (!$group) {
            $this->sendError('Group does not exist', 404);
            return;
        }

        if ($group->getCreatedBy() !== $userId) {
            $this->sendError('You cannnot edit group thats not yours', 403);
            return;
        }

        $clipboard = $this->clipboardRepository->findById($clipboardId);
        if (!$clipboard) {
            $this->sendError('Clipboard does not exist', 404);
            return;
        }

        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannnot add clipboard thats not yours to a group', 403);
            return;
        }

        $this->repository->addClipboardToGroup($clipboardId, $groupId);
        $this->sendResponse(['message' => 'Clipboard added to group'], 201);
    }

    private function removeClipboard(int $groupId, int $clipboardId, int $userId): void
    {
        $group = $this->repository->findById($groupId);
        if (!$group) {
            $this->sendError('Group does not exist', 404);
            return;
        }

        if ($group->getCreatedBy() !== $userId) {
            $this->sendError('You cannnot edit group thats not yours', 403);
            return;
        }

        $clipboard = $this->clipboardRepository->findById($clipboardId);
        if (!$clipboard) {
            $this->sendError('Clipboard does not exist', 404);
            return;
        }

        $this->repository->removeClipboardFromGroup($clipboardId, $groupId);
        $this->sendResponse(['message' => 'Clipboard removed from group']);
    }

    private function getClipboards(int $groupId, int $userId): void
    {
        $group = $this->repository->findById($groupId);
        if (!$group) {
            $this->sendError('Group does not exist', 404);
            return;
        }

        if ($group->getCreatedBy() !== $userId) {
            $this->sendError('You cannnot acess group thats not yours', 403);
            return;
        }

        $clipboards = $this->repository->getClipboardsByGroup($groupId);
        $this->sendResponse($clipboards);
    }

    private function toArray(ClipboardGroup $group): array
    {
        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'description' => $group->getDescription(),
            'created_by' => $group->getCreatedBy(),
            'created_at' => $group->getCreatedAt()
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
