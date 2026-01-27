<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminClipboardRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllClipboards($page = 1, $perPage = 25, $search = null, $filters = []) {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];
        $types = '';

        if ($search) {
            $where[] = "(c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= 'ss';
        }

        if (isset($filters['is_public'])) {
            $where[] = "c.is_public = ?";
            $params[] = (int)$filters['is_public'];
            $types .= 'i';
        }

        if (isset($filters['owner_id'])) {
            $where[] = "c.owner_id = ?";
            $params[] = (int)$filters['owner_id'];
            $types .= 'i';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM clipboards c $whereClause";
        $stmt = $this->db->prepare($countSql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];

        // Get clipboards
        $sql = "
            SELECT c.*, u.name as owner_name, u.email as owner_email,
                   (SELECT COUNT(*) FROM clipboard_items WHERE clipboard_id = c.id) as items_count,
                   (SELECT COUNT(*) FROM clipboard_subscriptions WHERE clipboard_id = c.id) as subscribers_count
            FROM clipboards c
            LEFT JOIN users u ON c.owner_id = u.id
            $whereClause
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $clipboards = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['allowed_content_types']) {
                $row['allowed_content_types'] = json_decode($row['allowed_content_types'], true);
            }
            $row['is_public'] = (bool)$row['is_public'];
            $clipboards[] = $row;
        }

        return [
            'clipboards' => $clipboards,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $total > 0 ? (int)ceil($total / $perPage) : 1
            ]
        ];
    }

    public function getClipboardDetails($clipboardId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as owner_name, u.email as owner_email
            FROM clipboards c
            LEFT JOIN users u ON c.owner_id = u.id
            WHERE c.id = ?
        ");
        $stmt->bind_param('i', $clipboardId);
        $stmt->execute();
        $clipboard = $stmt->get_result()->fetch_assoc();

        if (!$clipboard) {
            return null;
        }

        if ($clipboard['allowed_content_types']) {
            $clipboard['allowed_content_types'] = json_decode($clipboard['allowed_content_types'], true);
        }
        $clipboard['is_public'] = (bool)$clipboard['is_public'];

        // Get subscribers
        $stmt = $this->db->prepare("
            SELECT cs.*, u.name, u.email
            FROM clipboard_subscriptions cs
            LEFT JOIN users u ON cs.user_id = u.id
            WHERE cs.clipboard_id = ?
        ");
        $stmt->bind_param('i', $clipboardId);
        $stmt->execute();
        $result = $stmt->get_result();
        $clipboard['subscribers'] = [];
        while ($row = $result->fetch_assoc()) {
            $row['email_notifications'] = (bool)$row['email_notifications'];
            $clipboard['subscribers'][] = $row;
        }

        // Get recent items
        $stmt = $this->db->prepare("
            SELECT ci.*, u.name as submitted_by_name
            FROM clipboard_items ci
            LEFT JOIN users u ON ci.submitted_by = u.id
            WHERE ci.clipboard_id = ?
            ORDER BY ci.created_at DESC
            LIMIT 10
        ");
        $stmt->bind_param('i', $clipboardId);
        $stmt->execute();
        $result = $stmt->get_result();
        $clipboard['recent_items'] = [];
        while ($row = $result->fetch_assoc()) {
            $clipboard['recent_items'][] = $row;
        }

        return $clipboard;
    }

    public function updateClipboard($clipboardId, $data) {
        $allowedFields = ['name', 'description', 'is_public', 'max_subscribers', 'max_items', 'allowed_content_types', 'default_expiration_minutes'];
        $updates = [];
        $params = [];
        $types = '';

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = ?";
                if ($field === 'allowed_content_types' && is_array($value)) {
                    $params[] = json_encode($value);
                    $types .= 's';
                } else {
                    $params[] = $value;
                    $types .= is_int($value) ? 'i' : 's';
                }
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $clipboardId;
        $types .= 'i';

        $sql = "UPDATE clipboards SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function deleteClipboard($clipboardId) {
        $stmt = $this->db->prepare("DELETE FROM clipboards WHERE id = ?");
        $stmt->bind_param('i', $clipboardId);
        return $stmt->execute();
    }

    public function transferOwnership($clipboardId, $newOwnerId) {
        $stmt = $this->db->prepare("UPDATE clipboards SET owner_id = ? WHERE id = ?");
        $stmt->bind_param('ii', $newOwnerId, $clipboardId);
        return $stmt->execute();
    }
}
