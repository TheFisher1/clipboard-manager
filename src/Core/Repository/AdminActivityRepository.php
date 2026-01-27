<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminActivityRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getActivityLogs($filters = []) {
        $where = [];
        $params = [];
        $types = '';

        if (isset($filters['user_id'])) {
            $where[] = "ca.user_id = ?";
            $params[] = (int)$filters['user_id'];
            $types .= 'i';
        }

        if (isset($filters['clipboard_id'])) {
            $where[] = "ca.clipboard_id = ?";
            $params[] = (int)$filters['clipboard_id'];
            $types .= 'i';
        }

        if (isset($filters['action_type'])) {
            $where[] = "ca.action_type = ?";
            $params[] = $filters['action_type'];
            $types .= 's';
        }

        if (isset($filters['date_from'])) {
            $where[] = "DATE(ca.created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }

        if (isset($filters['date_to'])) {
            $where[] = "DATE(ca.created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 100;
        $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;

        $sql = "
            SELECT ca.*, u.name as user_name, u.email as user_email, c.name as clipboard_name
            FROM clipboard_activity ca
            LEFT JOIN users u ON ca.user_id = u.id
            LEFT JOIN clipboards c ON ca.clipboard_id = c.id
            $whereClause
            ORDER BY ca.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['details']) {
                $row['details'] = json_decode($row['details'], true);
            }
            $logs[] = $row;
        }

        return $logs;
    }

    public function exportActivityLogs($filters = []) {
        $logs = $this->getActivityLogs($filters);
        return $logs;
    }
}
