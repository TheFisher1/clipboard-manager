<?php

require_once __DIR__ . '/../../../Core/Repository/AdminActivityRepository.php';
require_once __DIR__ . '/../../../Services/AdminAuditService.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminActivityController {
    private $activityRepository;
    private $auditService;

    public function __construct() {
        $this->activityRepository = new AdminActivityRepository();
        $this->auditService = new AdminAuditService();
    }

    public function handleRequest($method, $action = null) {
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
            exit;
        }

        switch ($action) {
            case 'activity':
                $this->getActivityLogs();
                break;
            case 'export':
                $this->exportActivityLogs();
                break;
            case 'audit':
                $this->getAuditLogs();
                break;
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Endpoint not found']]);
                exit;
        }
    }

    private function getActivityLogs() {
        $filters = [];
        if (isset($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
        }
        if (isset($_GET['clipboard_id'])) {
            $filters['clipboard_id'] = $_GET['clipboard_id'];
        }
        if (isset($_GET['action_type'])) {
            $filters['action_type'] = $_GET['action_type'];
        }
        if (isset($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (isset($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        if (isset($_GET['limit'])) {
            $filters['limit'] = $_GET['limit'];
        }
        if (isset($_GET['offset'])) {
            $filters['offset'] = $_GET['offset'];
        }

        $logs = $this->activityRepository->getActivityLogs($filters);
        Response::success($logs);
    }

    private function exportActivityLogs() {
        $filters = [];
        if (isset($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (isset($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        $logs = $this->activityRepository->exportActivityLogs($filters);
        
        $format = $_GET['format'] ?? 'json';
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="activity_logs.csv"');
            
            $output = fopen('php://output', 'w');
            if (!empty($logs)) {
                fputcsv($output, array_keys($logs[0]));
                foreach ($logs as $log) {
                    fputcsv($output, $log);
                }
            }
            fclose($output);
            exit;
        } else {
            Response::success($logs);
        }
    }

    private function getAuditLogs() {
        $filters = [];
        if (isset($_GET['admin_user_id'])) {
            $filters['admin_user_id'] = $_GET['admin_user_id'];
        }
        if (isset($_GET['action_type'])) {
            $filters['action_type'] = $_GET['action_type'];
        }
        if (isset($_GET['target_type'])) {
            $filters['target_type'] = $_GET['target_type'];
        }
        if (isset($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (isset($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        if (isset($_GET['limit'])) {
            $filters['limit'] = $_GET['limit'];
        }
        if (isset($_GET['offset'])) {
            $filters['offset'] = $_GET['offset'];
        }

        $logs = $this->auditService->getAuditLogs($filters);
        Response::success($logs);
    }
}
