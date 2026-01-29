<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Services/SessionManager.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

// Initialize session and check admin authentication
SessionManager::initializeSession();

if (!AuthMiddleware::checkAdmin()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'ACCESS_DENIED',
            'message' => 'Admin access required'
        ]
    ]);
    exit;
}

// Get the request path and normalize it
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove script name from path if present (for XAMPP compatibility)
$scriptName = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

// Remove /api/admin prefix to get the actual route
$path = preg_replace('#^/api/admin#', '', $requestUri);
$path = '/' . trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];

try {
    // USER MANAGEMENT ROUTES
    if (preg_match('#^/users$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->handleRequest($method, null);
        exit;
    }

    if (preg_match('#^/users/(\d+)$#', $path, $matches)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->handleRequest($method, $matches[1]);
        exit;
    }

    if (preg_match('#^/users/(\d+)/reset-password$#', $path, $matches)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->handleRequest($method, $matches[1], 'reset-password');
        exit;
    }

    // DASHBOARD ROUTES
    if (preg_match('#^/dashboard/stats$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminDashboardController.php';
        $controller = new AdminDashboardController();
        $controller->handleRequest($method, 'stats');
        exit;
    }

    if (preg_match('#^/dashboard/recent-activity$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminDashboardController.php';
        $controller = new AdminDashboardController();
        $controller->handleRequest($method, 'recent-activity');
        exit;
    }

    // CLIPBOARD MANAGEMENT ROUTES
    if (preg_match('#^/clipboards$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminClipboardController.php';
        $controller = new AdminClipboardController();
        $controller->handleRequest($method, null);
        exit;
    }

    if (preg_match('#^/clipboards/(\d+)$#', $path, $matches)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminClipboardController.php';
        $controller = new AdminClipboardController();
        $controller->handleRequest($method, $matches[1]);
        exit;
    }

    if (preg_match('#^/clipboards/(\d+)/transfer$#', $path, $matches)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminClipboardController.php';
        $controller = new AdminClipboardController();
        $controller->handleRequest($method, $matches[1], 'transfer');
        exit;
    }

    // CONTENT MODERATION ROUTES
    if (preg_match('#^/content$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminContentController.php';
        $controller = new AdminContentController();
        $controller->handleRequest($method, null);
        exit;
    }

    if (preg_match('#^/content/(\d+)$#', $path, $matches)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminContentController.php';
        $controller = new AdminContentController();
        $controller->handleRequest($method, $matches[1]);
        exit;
    }

    if (preg_match('#^/content/bulk-delete$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminContentController.php';
        $controller = new AdminContentController();
        $controller->handleRequest($method, null, 'bulk-delete');
        exit;
    }

    // ACTIVITY LOG ROUTES
    if (preg_match('#^/activity$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminActivityController.php';
        $controller = new AdminActivityController();
        $controller->handleRequest($method, 'activity');
        exit;
    }

    if (preg_match('#^/activity/export$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminActivityController.php';
        $controller = new AdminActivityController();
        $controller->handleRequest($method, 'export');
        exit;
    }

    if (preg_match('#^/audit$#', $path)) {
        require_once __DIR__ . '/../../src/Controllers/Api/Admin/AdminActivityController.php';
        $controller = new AdminActivityController();
        $controller->handleRequest($method, 'audit');
        exit;
    }

    // Route not found
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'NOT_FOUND',
            'message' => 'Admin endpoint not found'
        ]
    ]);

} catch (Exception $e) {
    error_log('Admin API Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'message' => 'An internal error occurred',
            'details' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
