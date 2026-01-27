<?php

require_once __DIR__ . '/../../../config/config.php';

class AdminRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAllUsers(int $page = 1, int $perPage = 25, ?string $search = null, ?array $filters = null): array
    {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $whereClauses = [];
        $params = [];

        if ($search !== null && $search !== '') {
            $whereClauses[] = "(email LIKE :search OR name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (isset($filters['is_admin']) && $filters['is_admin'] !== null && $filters['is_admin'] !== '') {
            $whereClauses[] = "is_admin = :is_admin";
            $params[':is_admin'] = (int) $filters['is_admin'];
        }

        if (isset($filters['email_verified']) && $filters['email_verified'] !== null && $filters['email_verified'] !== '') {
            $whereClauses[] = "email_verified = :email_verified";
            $params[':email_verified'] = (int) $filters['email_verified'];
        }

        $whereSQL = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        $allowedSortFields = ['created_at', 'email', 'name', 'id'];
        $sortField = isset($filters['sort']) && in_array($filters['sort'], $allowedSortFields) 
            ? $filters['sort'] 
            : 'created_at';
        
        $order = isset($filters['order']) && strtoupper($filters['order']) === 'ASC' 
            ? 'ASC' 
            : 'DESC';

        $orderSQL = "ORDER BY {$sortField} {$order}";

        $countSQL = "SELECT COUNT(*) as total FROM users {$whereSQL}";
        $countStmt = $this->db->prepare($countSQL);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("
            SELECT 
                id,
                email,
                name,
                is_admin,
                email_verified,
                created_at,
                updated_at
            FROM users
            {$whereSQL}
            {$orderSQL}
            LIMIT :limit OFFSET :offset
        ");
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = array_map(function($user) {
            $user['is_admin'] = (bool) $user['is_admin'];
            $user['email_verified'] = (bool) $user['email_verified'];
            return $user;
        }, $users);

        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        return [
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Update user information
     * 
     * @param int $userId The user ID to update
     * @param array $data Associative array of fields to update
     * @return bool Success status
     */
    public function updateUser(int $userId, array $data): bool
    {
        $allowedFields = ['name', 'email', 'is_admin', 'email_verified'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "{$field} = :{$field}";
                $params[":{$field}"] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[':id'] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a user and all associated data
     * 
     * @param int $userId The user ID to delete
     * @return bool Success status
     */
    public function deleteUser(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get user statistics
     * 
     * @param int $userId The user ID
     * @return array User statistics
     */
    public function getUserStats(int $userId): array
    {
        // Get basic user info
        $stmt = $this->db->prepare("
            SELECT id, email, name, is_admin, email_verified, created_at
            FROM users
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [];
        }

        $user['is_admin'] = (bool) $user['is_admin'];
        $user['email_verified'] = (bool) $user['email_verified'];

        // Get clipboard count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM clipboards
            WHERE owner_id = :user_id
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user['clipboards_count'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get clipboard items count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM clipboard_items
            WHERE submitted_by = :user_id
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user['items_submitted'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get subscription count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM clipboard_subscriptions
            WHERE user_id = :user_id
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user['subscriptions_count'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $user;
    }

    /**
     * Get a single user by ID
     * 
     * @param int $userId The user ID
     * @return array|null User data or null if not found
     */
    public function getUserById(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, email, name, is_admin, email_verified, created_at, updated_at
            FROM users
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        $user['is_admin'] = (bool) $user['is_admin'];
        $user['email_verified'] = (bool) $user['email_verified'];

        return $user;
    }

    /**
     * Reset user password
     * 
     * @param int $userId The user ID
     * @param string $newPassword The new password (will be hashed)
     * @return bool Success status
     */
    public function resetUserPassword(int $userId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = :password_hash
            WHERE id = :id
        ");
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
