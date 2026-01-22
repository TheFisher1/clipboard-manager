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

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Services/SessionManager.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('#^/auth/(login|register|logout|me)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/AuthController.php';
    $controller = new ApiAuthController();
    $controller->handleRequest($method, $matches[1]);
    exit;
}

SessionManager::initializeSession();
if (!SessionManager::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if (preg_match('#^/subscriptions/user/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();

    $userId = $matches[1];
    $controller->handleRequest($method, null, $userId);
    exit;
}

if (preg_match('#^/subscriptions/clipboard/(\d+)/user/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();

    $clipboardId = $matches[1];
    $userId = $matches[2];
    $controller->handleRequest($method, $clipboardId, $userId);
    exit;
}

if ($path === '/subscriptions' && $method === 'POST') {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();
    $controller->handleRequest($method, null, null);
    exit;
}

if (preg_match('#^/subscriptions/clipboard/(\d+)/user/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();

    $clipboardId = $matches[1];
    $userId = $matches[2];
    $controller->handleRequest($method, $clipboardId, $userId);
    exit;
}

if (preg_match('#^/subscriptions/clipboard/(\d+)/user/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();

    $clipboardId = $matches[1];
    $userId = $matches[2];
    $controller->handleRequest($method, $clipboardId, $userId);
    exit;
}

if (preg_match('#^/clipboards(/(\d+))?(/items)?(/(\d+))?$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardController.php';
    $controller = new ClipboardController();
    
    $clipboardId = $matches[2] ?? null;
    $isItems = isset($matches[3]);
    $itemId = $matches[5] ?? null;
    
    if ($isItems) {
        require_once __DIR__ . '/../src/Controllers/Api/ClipboardItemController.php';
        $itemController = new ClipboardItemController();
        $itemController->handleRequest($method, $clipboardId, $itemId);
    } else {
        $controller->handleRequest($method, $clipboardId);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
