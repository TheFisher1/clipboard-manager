<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../Services/AdminAuditService.php';
require_once __DIR__ . '/../../../Services/SessionManager.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminSettingsController {
    private $db;
    private $auditService;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auditService = new AdminAuditService();
    }

    public function handleRequest($method, $settingKey = null) {
        switch ($method) {
            case 'GET':
                if ($settingKey) {
                    $this->getSetting($settingKey);
                } else {
                    $this->getAllSettings();
                }
                break;
            case 'PUT':
                if ($settingKey) {
                    $this->updateSetting($settingKey);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
                exit;
        }
    }

    private function getAllSettings() {
        $stmt = $this->db->query("SELECT * FROM system_settings ORDER BY category, setting_key");
        $result = $stmt->get_result();
        
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_public'] = (bool)$row['is_public'];
            $settings[] = $row;
        }

        Response::success($settings);
    }

    private function getSetting($settingKey) {
        $stmt = $this->db->prepare("SELECT * FROM system_settings WHERE setting_key = ?");
        $stmt->bind_param('s', $settingKey);
        $stmt->execute();
        $setting = $stmt->get_result()->fetch_assoc();

        if (!$setting) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => ['code' => 'SETTING_NOT_FOUND', 'message' => 'Setting not found']]);
            exit;
        }

        $setting['is_public'] = (bool)$setting['is_public'];
        Response::success($setting);
    }

    private function updateSetting($settingKey) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['setting_value'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => ['code' => 'INVALID_DATA', 'message' => 'setting_value is required']]);
            exit;
        }

        // Get old value for audit
        $stmt = $this->db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->bind_param('s', $settingKey);
        $stmt->execute();
        $oldSetting = $stmt->get_result()->fetch_assoc();

        $stmt = $this->db->prepare("
            UPDATE system_settings 
            SET setting_value = ?, updated_by = ?
            WHERE setting_key = ?
        ");
        $userId = SessionManager::getCurrentUserId();
        $stmt->bind_param('sis', $data['setting_value'], $userId, $settingKey);
        $success = $stmt->execute();

        if ($success) {
            $this->auditService->logAction(
                $userId,
                'setting_update',
                'settings',
                null,
                [
                    'setting_key' => $settingKey,
                    'old_value' => $oldSetting['setting_value'] ?? null,
                    'new_value' => $data['setting_value']
                ]
            );

            Response::success(['message' => 'Setting updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => ['code' => 'UPDATE_FAILED', 'message' => 'Failed to update setting']]);
            exit;
        }
    }
}
