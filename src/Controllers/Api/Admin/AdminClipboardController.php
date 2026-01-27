<?php

require_once __DIR__ . '/../../../Core/Repository/AdminClipboardRepository.php';
require_once __DIR__ . '/../../../Services/AdminAuditService.php';
require_once __DIR__ . '/../../../Services/SessionManager.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminClipboardController {
    private $repository;
    private $auditService;

    public function __construct() {
        $this->repository = new AdminClipboardRepository();
        $this->auditService = new AdminAuditService();
    }

    public function handleRequest($method, $clipboardId = null, $action = null) {
        switch ($method) {
            case 'GET':
                if ($clipboardId) {
                    $this->getClipboardDetail($clipboardId);
                } else {
                    $this->listClipboards();
                }
                break;
            case 'PUT':
                if ($clipboardId) {
                    $this->updateClipboard($clipboardId);
                }
                break;
            case 'DELETE':
                if ($clipboardId) {
                    $this->deleteClipboard($clipboardId);
                }
                break;
            case 'POST':
                if ($action === 'transfer' && $clipboardId) {
                    $this->transferOwnership($clipboardId);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
                exit;
        }
    }

    private function listClipboards() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;
        $search = $_GET['search'] ?? null;
        
        $filters = [];
        if (isset($_GET['is_public'])) {
            $filters['is_public'] = $_GET['is_public'];
        }
        if (isset($_GET['owner_id'])) {
            $filters['owner_id'] = $_GET['owner_id'];
        }

        $result = $this->repository->getAllClipboards($page, $perPage, $search, $filters);
        Response::success($result);
    }

    private function getClipboardDetail($clipboardId) {
        $clipboard = $this->repository->getClipboardDetails($clipboardId);
        
        if (!$clipboard) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => ['code' => 'CLIPBOARD_NOT_FOUND', 'message' => 'Clipboard not found']]);
            exit;
        }

        Response::success($clipboard);
    }

    private function updateClipboard($clipboardId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => ['code' => 'INVALID_DATA', 'message' => 'Invalid request data']]);
            exit;
        }

        $success = $this->repository->updateClipboard($clipboardId, $data);
        
        if ($success) {
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'clipboard_update',
                'clipboard',
                $clipboardId,
                ['updated_fields' => array_keys($data)]
            );

            Response::success(['message' => 'Clipboard updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'UPDATE_FAILED', 'message' => 'Failed to update clipboard']]);
            exit;
        }
    }

    private function deleteClipboard($clipboardId) {
        $success = $this->repository->deleteClipboard($clipboardId);
        
        if ($success) {
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'clipboard_delete',
                'clipboard',
                $clipboardId,
                []
            );

            Response::success(['message' => 'Clipboard deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'DELETE_FAILED', 'message' => 'Failed to delete clipboard']]);
            exit;
        }
    }

    private function transferOwnership($clipboardId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['new_owner_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => ['code' => 'INVALID_DATA', 'message' => 'new_owner_id is required']]);
            exit;
        }

        $success = $this->repository->transferOwnership($clipboardId, $data['new_owner_id']);
        
        if ($success) {
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'clipboard_transfer',
                'clipboard',
                $clipboardId,
                ['new_owner_id' => $data['new_owner_id']]
            );

            Response::success(['message' => 'Ownership transferred successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'TRANSFER_FAILED', 'message' => 'Failed to transfer ownership']]);
            exit;
        }
    }
}
