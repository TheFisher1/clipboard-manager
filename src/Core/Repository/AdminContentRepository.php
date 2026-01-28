<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminContentRepository {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAllContent($page = 1, $perPage = 25, $filters = []) {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (isset($filters['content_type'])) {
            $where[] = "ci.content_type = ?";
            $params[] = $filters['content_type'];
        }

        if (isset($filters['clipboard_id'])) {
            $where[] = "ci.clipboard_id = ?";
            $params[] = (int)$filters['clipboard_id'];
        }

        if (isset($filters['date_from'])) {
            $where[] = "DATE(ci.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $where[] = "DATE(ci.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM clipboard_items ci $whereClause";
        $stmt = $this->db->prepare($countSql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $total = (int)$stmt->fetchColumn();

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

        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        $stmt->execute([$contentId]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($content) {
            $content['is_single_use'] = (bool)$content['is_single_use'];
            $content['is_consumed'] = (bool)$content['is_consumed'];
        }

        return $content;
    }

    public function deleteContent($contentId) {
        $stmt = $this->db->prepare("DELETE FROM clipboard_items WHERE id = ?");
        return $stmt->execute([$contentId]);
    }

    public function bulkDeleteContent($contentIds) {
        if (empty($contentIds)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($contentIds), '?'));
        $sql = "DELETE FROM clipboard_items WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($contentIds);
    }
}
