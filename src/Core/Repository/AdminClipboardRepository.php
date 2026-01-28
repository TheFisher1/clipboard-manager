<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminClipboardRepository {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAllClipboards($page = 1, $perPage = 25, $search = null, $filters = []) {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (isset($filters['is_public'])) {
            $where[] = "c.is_public = ?";
            $params[] = (int)$filters['is_public'];
        }

        if (isset($filters['owner_id'])) {
            $where[] = "c.owner_id = ?";
            $params[] = (int)$filters['owner_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM clipboards c $whereClause";
        $stmt = $this->db->prepare($countSql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $total = (int)$stmt->fetchColumn();

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

        $stmt = $this->db->prepare($sql);
        // Bind all params except the last two (limit and offset)
        $paramCount = count($params);
        for ($i = 0; $i < $paramCount - 2; $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
        // Bind limit and offset as integers
        $stmt->bindValue($paramCount - 1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($paramCount, $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $clipboards = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        $stmt->execute([$clipboardId]);
        $clipboard = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $stmt->execute([$clipboardId]);
        $clipboard['subscribers'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        $stmt->execute([$clipboardId]);
        $clipboard['recent_items'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clipboard['recent_items'][] = $row;
        }

        return $clipboard;
    }

    public function updateClipboard($clipboardId, $data) {
        $allowedFields = ['name', 'description', 'is_public', 'max_subscribers', 'max_items', 'allowed_content_types', 'default_expiration_minutes'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = ?";
                if ($field === 'allowed_content_types' && is_array($value)) {
                    $params[] = json_encode($value);
                } else {
                    $params[] = $value;
                }
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $clipboardId;

        $sql = "UPDATE clipboards SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteClipboard($clipboardId) {
        $stmt = $this->db->prepare("DELETE FROM clipboards WHERE id = ?");
        return $stmt->execute([$clipboardId]);
    }

    public function transferOwnership($clipboardId, $newOwnerId) {
        $stmt = $this->db->prepare("UPDATE clipboards SET owner_id = ? WHERE id = ?");
        return $stmt->execute([$newOwnerId, $clipboardId]);
    }
}
