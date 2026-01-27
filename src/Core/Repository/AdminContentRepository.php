<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminContentRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllContent($page = 1, $perPage = 25, $filters = []) {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];
        $types = '';

        if (isset($filters['content_type'])) {
            $where[] = "ci.content_type = ?";
            $params[] = $filters['content_type'];
            $types .= 's';
        }

        if (isset($filters['clipboard_id'])) {
            $where[] = "ci.clipboard_id = ?";
            $params[] = (int)$filters['clipboard_id'];
            $types .= 'i';
        }

        if (isset($filters['date_from'])) {
            $where[] = "DATE(ci.created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }

        if (isset($filters['date_to'])) {
            $where[] = "DATE(ci.created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM clipboard_items ci $whereClause";
        $stmt = $this->db->prepare($countSql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];

        // Get content
        $sql = "
            SELECT ci.*, c.name as clipboard_name, u.name as submitted_by_name
            FROM clipboard_items ci
            LEFT JOIN clipboards c ON ci.clipboard_id = c.id
            LEFT JOIN users u ON ci.submitted_by = u.id
            $whereClause
            ORDER BY ci.created_at DESC
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

        $content = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_single_use'] = (bool)$row['is_single_use'];
            $row['is_consumed'] = (bool)$row['is_consumed'];
            $content[] = $row;
        }

        return [
            'content' => $content,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $total > 0 ? (int)ceil($total / $perPage) : 1
            ]
        ];
    }

    public function getContentDetails($contentId) {
        $stmt = $this->db->prepare("
            SELECT ci.*, c.name as clipboard_name, u.name as submitted_by_name, u.email as submitted_by_email
            FROM clipboard_items ci
            LEFT JOIN clipboards c ON ci.clipboard_id = c.id
            LEFT JOIN users u ON ci.submitted_by = u.id
            WHERE ci.id = ?
        ");
        $stmt->bind_param('i', $contentId);
        $stmt->execute();
        $content = $stmt->get_result()->fetch_assoc();

        if ($content) {
            $content['is_single_use'] = (bool)$content['is_single_use'];
            $content['is_consumed'] = (bool)$content['is_consumed'];
        }

        return $content;
    }

    public function deleteContent($contentId) {
        $stmt = $this->db->prepare("DELETE FROM clipboard_items WHERE id = ?");
        $stmt->bind_param('i', $contentId);
        return $stmt->execute();
    }

    public function bulkDeleteContent($contentIds) {
        if (empty($contentIds)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($contentIds), '?'));
        $sql = "DELETE FROM clipboard_items WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        $types = str_repeat('i', count($contentIds));
        $stmt->bind_param($types, ...$contentIds);
        
        return $stmt->execute();
    }
}
