<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardGroupRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardGroup.php';

class ClipboardGroupController
{
    private ClipboardGroupRepository $repository;

    public function __construct()
    {
        $this->repository = new ClipboardGroupRepository();
    }

    public function handleRequest(string $method, ?string $id, ?string $action = null, ?string $clipboardId = null): void
    {
        try {
            switch ($method) {
                case 'GET':
                    if ($id && $action === 'clipboards') {
                        $this->getClipboards((int)$id);
                    } elseif ($id) {
                        $this->getOne((int)$id);
                    } else {
                        $this->getAll();
                    }
                    break;

                case 'POST':
                    if ($id && $action === 'clipboards' && $clipboardId) {
                        $this->addClipboard((int)$id, (int)$clipboardId);
                    } else {
                        $this->create();
                    }
                    break;

                case 'DELETE':
                    if ($id && $action === 'clipboards' && $clipboardId) {
                        $this->removeClipboard((int)$id, (int)$clipboardId);
                    } elseif ($id) {
                        $this->delete((int)$id);
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

    private function getOne(int $id): void
    {
        $group = $this->repository->findById($id);
        if (!$group) {
            $this->sendError('Group not found', 404);
            return;
        }
        $this->sendResponse($this->toArray($group));
    }

    private function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['created_by'])) {
            $this->sendError('Missing required fields: name, created_by', 400);
            return;
        }

        $group = new ClipboardGroup(
            $data['name'],
            (int)$data['created_by'],
            $data['description'] ?? null
        );

        $id = $this->repository->create($group);
        $created = $this->repository->findById($id);

        $this->sendResponse($this->toArray($created), 201);
    }

    private function delete(int $id): void
    {
        $group = $this->repository->findById($id);
        if (!$group) {
            $this->sendError('Group not found', 404);
            return;
        }

        $this->repository->delete($id);
        $this->sendResponse(['message' => 'Group deleted successfully']);
    }

    private function addClipboard(int $groupId, int $clipboardId): void
    {
        $this->repository->addClipboardToGroup($clipboardId, $groupId);
        $this->sendResponse(['message' => 'Clipboard added to group'], 201);
    }

    private function removeClipboard(int $groupId, int $clipboardId): void
    {
        $this->repository->removeClipboardFromGroup($clipboardId, $groupId);
        $this->sendResponse(['message' => 'Clipboard removed from group']);
    }

    private function getClipboards(int $groupId): void
    {
        $clipboards = $this->repository->getClipboardsByGroup($groupId);
        $this->sendResponse($clipboards);
    }

    private function toArray(ClipboardGroup $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'created_by' => $group->created_by,
            'created_at' => $group->created_at
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
