<?php

require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminDashboardController {
    private PDO $db;
    private const CACHE_TTL = 300; // 5 minutes cache

    public function __construct() {
        $this->db = getDB();
    }

    public function handleRequest($method, $action = null) {
        switch ($method) {
            case 'GET':
                if ($action === 'stats') {
                    $this->getStats();
                } elseif ($action === 'recent-activity') {
                    $this->getRecentActivity();
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => [
                            'code' => 'NOT_FOUND',
                            'message' => 'Endpoint not found'
                        ]
                    ]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'METHOD_NOT_ALLOWED',
                        'message' => 'Method not allowed'
                    ]
                ]);
                exit;
        }
    }

    /**
     * GET /api/admin/dashboard/stats - Get dashboard statistics
     * Optimized with single query for multiple counts and caching
     */
    private function getStats() {
        // Check if we should bypass cache (force refresh)
        $forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] === 'true';
        
        // Try to get from cache first
        if (!$forceRefresh) {
            $cached = $this->getCachedStats();
            if ($cached !== null) {
                Response::success($cached);
                return;
            }
        }

        // Use a single query to get all basic counts for better performance
        $stmt = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM users WHERE is_admin = 1) as admin_users,
                (SELECT COUNT(*) FROM users WHERE email_verified = 1) as verified_users,
                (SELECT COUNT(*) FROM clipboards) as total_clipboards,
                (SELECT COUNT(*) FROM clipboards WHERE is_public = 1) as public_clipboards,
                (SELECT COUNT(*) FROM clipboard_items) as total_items,
                (SELECT COUNT(*) FROM clipboard_items WHERE expires_at IS NOT NULL AND expires_at > NOW()) as active_items,
                (SELECT COUNT(*) FROM clipboard_subscriptions) as total_subscriptions,
                (SELECT COUNT(*) FROM clipboard_activity WHERE DATE(created_at) = CURDATE()) as today_activities
        ");
        
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Convert to integers
        foreach ($stats as $key => $value) {
            $stats[$key] = (int) $value;
        }

        // Get recent growth statistics (last 7 days)
        $stmt = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_users_week,
                (SELECT COUNT(*) FROM clipboards WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_clipboards_week,
                (SELECT COUNT(*) FROM clipboard_items WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_items_week
        ");
        
        $growth = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($growth as $key => $value) {
            $stats[$key] = (int) $value;
        }

        // Get storage statistics
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(file_size), 0) as total_storage_bytes,
                COUNT(*) as files_count
            FROM clipboard_items 
            WHERE file_path IS NOT NULL
        ");
        
        $storage = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_storage_bytes'] = (int) $storage['total_storage_bytes'];
        $stats['total_files'] = (int) $storage['files_count'];

        // Cache the results
        $this->cacheStats($stats);

        Response::success($stats);
    }

    /**
     * Get cached statistics from database
     */
    private function getCachedStats(): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT setting_value, updated_at 
                FROM system_settings 
                WHERE setting_key = 'dashboard_stats_cache'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            // Check if cache is still valid
            $cacheTime = strtotime($result['updated_at']);
            if (time() - $cacheTime > self::CACHE_TTL) {
                return null;
            }

            return json_decode($result['setting_value'], true);
        } catch (Exception $e) {
            error_log('Cache retrieval error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache statistics in database
     */
    private function cacheStats(array $stats): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description)
                VALUES ('dashboard_stats_cache', :value, 'json', 'cache', 'Cached dashboard statistics')
                ON DUPLICATE KEY UPDATE 
                    setting_value = :value,
                    updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->bindValue(':value', json_encode($stats), PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            error_log('Cache storage error: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/admin/dashboard/recent-activity - Get recent activity
     * Optimized with indexed queries and limited joins
     */
    private function getRecentActivity() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $limit = min(max(1, $limit), 50);

        // Use indexed columns for better performance
        $stmt = $this->db->prepare("
            SELECT 
                ca.id,
                ca.action_type,
                ca.created_at,
                ca.details,
                u.name as user_name,
                u.email as user_email,
                c.name as clipboard_name,
                ci.title as item_title
            FROM clipboard_activity ca
            INNER JOIN users u ON ca.user_id = u.id
            LEFT JOIN clipboards c ON ca.clipboard_id = c.id
            LEFT JOIN clipboard_items ci ON ca.item_id = ci.id
            ORDER BY ca.created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse JSON details
        foreach ($activities as &$activity) {
            if ($activity['details']) {
                $activity['details'] = json_decode($activity['details'], true);
            }
        }

        Response::success(['activities' => $activities]);
    }
}
