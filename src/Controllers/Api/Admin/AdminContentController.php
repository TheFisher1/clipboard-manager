<?php

require_once __DIR__ . '/../../../Core/Repository/AdminContentRepository.php';
require_once __DIR__ . '/../../../Services/AdminAuditService.php';
require_once __DIR__ . '/../../../Services/SessionManager.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminContentController {
    private $repository;
    private $auditService;

    public function __construct() {
        $this->repository = new AdminContentRepository();
        $this->auditService = new AdminAuditService();
    }

    public function handleRequest($method, $contentId = null, $action = null) {
        switch ($method) {
            case 'GET':
                if ($contentId) {
                    $this->getContentDetail($contentId);
                } else {
                    $this->listContent();
                }
                break;
            case 'DELETE':
                if ($contentId) {
                    $this->deleteContent($contentId);
                }
                break;
            case 'POST':
                if ($action === 'bulk-delete') {
                    $this->bulkDeleteContent();
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
                exit;
        }
    }

    private function listContent() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;
        
        $filters = [];
        if (isset($_GET['content_type'])) {
            $filters['content_type'] = $_GET['content_type'];
        }
        if (isset($_GET['clipboard_id'])) {
            $filters['clipboard_id'] = $_GET['clipboard_id'];
        }
        if (isset($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (isset($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        $result = $this->repository->getAllContent($page, $perPage, $filters);
        Response::success($result);
    }

    private function getContentDetail($contentId) {
        $content = $this->repository->getContentDetails($contentId);
        
        if (!$content) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => ['code' => 'CONTENT_NOT_FOUND', 'message' => 'Content not found']]);
            exit;
        }

        Response::success($content);
    }

    private function deleteContent($contentId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $reason = $data['reason'] ?? 'No reason provided';

        $success = $this->repository->deleteContent($contentId);
        
        if ($success) {
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'content_delete',
                'content',
                $contentId,
                ['reason' => $reason]
            );

            Response::success(['message' => 'Content deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'DELETE_FAILED', 'message' => 'Failed to delete content']]);
            exit;
        }
    }

    private function bulkDeleteContent() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['content_ids']) || !is_array($data['content_ids'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => ['code' => 'INVALID_DATA', 'message' => 'content_ids array is required']]);
            exit;
        }

        $reason = $data['reason'] ?? 'Bulk deletion';
        $success = $this->repository->bulkDeleteContent($data['content_ids']);
        
        if ($success) {
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'content_bulk_delete',
                'content',
                null,
                ['content_ids' => $data['content_ids'], 'reason' => $reason, 'count' => count($data['content_ids'])]
            );

            Response::success(['message' => 'Content deleted successfully', 'count' => count($data['content_ids'])]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'DELETE_FAILED', 'message' => 'Failed to delete content']]);
            exit;
        }
    }
}
