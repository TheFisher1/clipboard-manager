<?php

require_once __DIR__ . '/../../config/config.php';

class AdminAuditService {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

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

            return $stmt->execute([
                $adminUserId,
                $actionType,
                $targetType,
                $targetId,
                $detailsJson,
                $ipAddress,
                $userAgent
            ]);
        } catch (Exception $e) {
            error_log('AdminAuditService::logAction error: ' . $e->getMessage());
            return false;
        }
    }

    public function getAuditLogs($filters = []) {
        try {
            $where = [];
            $params = [];

            if (isset($filters['admin_user_id'])) {
                $where[] = 'aal.admin_user_id = ?';
                $params[] = $filters['admin_user_id'];
            }

            if (isset($filters['action_type'])) {
                $where[] = 'aal.action_type = ?';
                $params[] = $filters['action_type'];
            }

            if (isset($filters['target_type'])) {
                $where[] = 'aal.target_type = ?';
                $params[] = $filters['target_type'];
            }

            if (isset($filters['target_id'])) {
                $where[] = 'aal.target_id = ?';
                $params[] = $filters['target_id'];
            }

            if (isset($filters['date_from'])) {
                $where[] = 'DATE(aal.created_at) >= ?';
                $params[] = $filters['date_from'];
            }

            if (isset($filters['date_to'])) {
                $where[] = 'DATE(aal.created_at) <= ?';
                $params[] = $filters['date_to'];
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

            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            // Bind all params except the last two (limit and offset)
            $paramCount = count($params);
            for ($i = 0; $i < $paramCount - 2; $i++) {
                $stmt->bindValue($i + 1, $params[$i]);
            }
            // Bind limit and offset as integers
            $stmt->bindValue($paramCount - 1, $limit, PDO::PARAM_INT);
            $stmt->bindValue($paramCount, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $logs = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['action_details']) {
                    $row['action_details'] = json_decode($row['action_details'], true);
                }
                $logs[] = $row;
            }

            return $logs;
        } catch (Exception $e) {
            error_log('AdminAuditService::getAuditLogs error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAuditLogsCount($filters = []) {
        try {
            $where = [];
            $params = [];

            if (isset($filters['admin_user_id'])) {
                $where[] = 'admin_user_id = ?';
                $params[] = $filters['admin_user_id'];
            }

            if (isset($filters['action_type'])) {
                $where[] = 'action_type = ?';
                $params[] = $filters['action_type'];
            }

            if (isset($filters['target_type'])) {
                $where[] = 'target_type = ?';
                $params[] = $filters['target_type'];
            }

            if (isset($filters['target_id'])) {
                $where[] = 'target_id = ?';
                $params[] = $filters['target_id'];
            }

            if (isset($filters['date_from'])) {
                $where[] = 'DATE(created_at) >= ?';
                $params[] = $filters['date_from'];
            }

            if (isset($filters['date_to'])) {
                $where[] = 'DATE(created_at) <= ?';
                $params[] = $filters['date_to'];
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $sql = "SELECT COUNT(*) as total FROM admin_audit_log $whereClause";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log('AdminAuditService::getAuditLogsCount error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getRecentAdminActions($adminUserId, $limit = 10) {
        return $this->getAuditLogs([
            'admin_user_id' => $adminUserId,
            'limit' => $limit,
            'offset' => 0
        ]);
    }

    public function getTargetAuditLogs($targetType, $targetId, $limit = 50) {
        return $this->getAuditLogs([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'limit' => $limit,
            'offset' => 0
        ]);
    }
}
