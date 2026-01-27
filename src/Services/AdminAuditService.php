<?php

require_once __DIR__ . '/../config/config.php';

class AdminAuditService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Log an administrative action
     * 
     * @param int $adminUserId The ID of the admin performing the action
     * @param string $actionType The type of action (e.g., 'user_update', 'clipboard_delete')
     * @param string $targetType The type of target ('user', 'clipboard', 'content', 'settings')
     * @param int|null $targetId The ID of the target entity
     * @param array|null $actionDetails Additional details about the action
     * @return bool Success status
     */
    public function logAction($adminUserId, $actionType, $targetType, $targetId = null, $actionDetails = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO admin_audit_log 
                (admin_user_id, action_type, target_type, target_id, action_details, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $detailsJson = $actionDetails ? json_encode($actionDetails) : null;

            $stmt->bind_param(
                'issssss',
                $adminUserId,
                $actionType,
                $targetType,
                $targetId,
                $detailsJson,
                $ipAddress,
                $userAgent
            );

            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log('AdminAuditService::logAction error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get audit logs with optional filtering
     * 
     * @param array $filters Associative array of filter criteria
     *   - admin_user_id: Filter by admin user
     *   - action_type: Filter by action type
     *   - target_type: Filter by target type
     *   - target_id: Filter by target ID
     *   - date_from: Filter by start date (YYYY-MM-DD)
     *   - date_to: Filter by end date (YYYY-MM-DD)
     *   - limit: Number of records to return (default: 100)
     *   - offset: Offset for pagination (default: 0)
     * @return array Array of audit log entries
     */
    public function getAuditLogs($filters = []) {
        try {
            $where = [];
            $params = [];
            $types = '';

            if (isset($filters['admin_user_id'])) {
                $where[] = 'aal.admin_user_id = ?';
                $params[] = $filters['admin_user_id'];
                $types .= 'i';
            }

            if (isset($filters['action_type'])) {
                $where[] = 'aal.action_type = ?';
                $params[] = $filters['action_type'];
                $types .= 's';
            }

            if (isset($filters['target_type'])) {
                $where[] = 'aal.target_type = ?';
                $params[] = $filters['target_type'];
                $types .= 's';
            }

            if (isset($filters['target_id'])) {
                $where[] = 'aal.target_id = ?';
                $params[] = $filters['target_id'];
                $types .= 'i';
            }

            if (isset($filters['date_from'])) {
                $where[] = 'DATE(aal.created_at) >= ?';
                $params[] = $filters['date_from'];
                $types .= 's';
            }

            if (isset($filters['date_to'])) {
                $where[] = 'DATE(aal.created_at) <= ?';
                $params[] = $filters['date_to'];
                $types .= 's';
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $limit = isset($filters['limit']) ? (int)$filters['limit'] : 100;
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;

            $sql = "
                SELECT 
                    aal.*,
                    u.name as admin_name,
                    u.email as admin_email
                FROM admin_audit_log aal
                LEFT JOIN users u ON aal.admin_user_id = u.id
                $whereClause
                ORDER BY aal.created_at DESC
                LIMIT ? OFFSET ?
            ";

            $stmt = $this->db->prepare($sql);
            
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $logs = [];

            while ($row = $result->fetch_assoc()) {
                if ($row['action_details']) {
                    $row['action_details'] = json_decode($row['action_details'], true);
                }
                $logs[] = $row;
            }

            $stmt->close();
            return $logs;
        } catch (Exception $e) {
            error_log('AdminAuditService::getAuditLogs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of audit logs matching filters
     * 
     * @param array $filters Same filters as getAuditLogs
     * @return int Total count
     */
    public function getAuditLogsCount($filters = []) {
        try {
            $where = [];
            $params = [];
            $types = '';

            if (isset($filters['admin_user_id'])) {
                $where[] = 'admin_user_id = ?';
                $params[] = $filters['admin_user_id'];
                $types .= 'i';
            }

            if (isset($filters['action_type'])) {
                $where[] = 'action_type = ?';
                $params[] = $filters['action_type'];
                $types .= 's';
            }

            if (isset($filters['target_type'])) {
                $where[] = 'target_type = ?';
                $params[] = $filters['target_type'];
                $types .= 's';
            }

            if (isset($filters['target_id'])) {
                $where[] = 'target_id = ?';
                $params[] = $filters['target_id'];
                $types .= 'i';
            }

            if (isset($filters['date_from'])) {
                $where[] = 'DATE(created_at) >= ?';
                $params[] = $filters['date_from'];
                $types .= 's';
            }

            if (isset($filters['date_to'])) {
                $where[] = 'DATE(created_at) <= ?';
                $params[] = $filters['date_to'];
                $types .= 's';
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT COUNT(*) as total FROM admin_audit_log $whereClause";
            $stmt = $this->db->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return (int)$row['total'];
        } catch (Exception $e) {
            error_log('AdminAuditService::getAuditLogsCount error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent audit logs for a specific admin user
     * 
     * @param int $adminUserId The admin user ID
     * @param int $limit Number of records to return
     * @return array Array of recent audit log entries
     */
    public function getRecentAdminActions($adminUserId, $limit = 10) {
        return $this->getAuditLogs([
            'admin_user_id' => $adminUserId,
            'limit' => $limit,
            'offset' => 0
        ]);
    }

    /**
     * Get audit logs for a specific target
     * 
     * @param string $targetType The target type
     * @param int $targetId The target ID
     * @param int $limit Number of records to return
     * @return array Array of audit log entries
     */
    public function getTargetAuditLogs($targetType, $targetId, $limit = 50) {
        return $this->getAuditLogs([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'limit' => $limit,
            'offset' => 0
        ]);
    }
}
