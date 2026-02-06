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
// Remove everything before /api/ and the /api itself
// e.g., /clipboard_manager/api/auth/login -> /auth/login
if (preg_match('#^(.*?)/api(/.*)?$#', $path, $matches)) {
    $path = $matches[2] ?? '/';
}
$method = $_SERVER['REQUEST_METHOD'];

// AUTH ROUTES

if (preg_match('#^/auth/(login|register|logout|me)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/AuthController.php';
    $controller = new ApiAuthController();
    $controller->handleRequest($method, $matches[1]);
    exit;
}

if (preg_match('#^/users/(\d+)$#', $path, $matches) && $method === 'GET') {
    require_once __DIR__ . '/../src/Controllers/Api/UserController.php';
    $controller = new UserController();

    $userId = (int)$matches[1];
    $controller->getById($userId);
    exit;
}

SessionManager::initializeSession();
if (!SessionManager::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = SessionManager::getCurrentUserId();

// SUBSCRIPTION ROUTES

if ($path === '/subscriptions') {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();
    $controller->handleRequest($method, null, $userId);
    exit;
}

if (preg_match('#^/subscriptions/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardSubscriptionController.php';
    $controller = new ClipboardSubscriptionController();

    $clipboardId = $matches[1];
    $controller->handleRequest($method, $clipboardId, $userId);
    exit;
}

// CLIPBOARD ACTIVITY ROUTES

if (preg_match(
    '#^/actions(?:/(user|clipboard|item))?(?:/(\d+))?$#',
    $path,
    $matches
)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardActivityController.php';
    $controller = new ClipboardActivityController();

    $type = $matches[1] ?? null;
    $id   = $matches[2] ?? null;

    if ($type === null && $id !== null) {
        $type = 'id';
    }

    $controller->handleRequest($method, $type, $id, $userId);
    exit;
}

// CLIPBOARD GROUP ROUTES

if (preg_match('#^/groups(?:/(\d+))?(?:/clipboards(?:/(\d+))?)?$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardGroupController.php';
    $controller = new ClipboardGroupController();

    $groupId = $matches[1] ?? null;
    $clipboardId = $matches[2] ?? null;
    $isClipboards = strpos($path, '/clipboards') !== false;

    if ($isClipboards) {
        $controller->handleRequest($method, $groupId, $userId, 'clipboards', $clipboardId);
    } else {
        $controller->handleRequest($method, $groupId, $userId);
    }
    exit;
}

if (preg_match('#^/clipboards/mine$#', $path)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardController.php';
    $controller = new ClipboardController();
    $controller->handleRequest($method, null, $userId, 'mine');
    exit;
}

if (preg_match('#^/clipboards/(\d+)/items/file$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardItemController.php';
    $controller = new ClipboardItemController();

    $clipboardId = $matches[1];
    $controller->handleRequest($method, 'file', $clipboardId, null, $userId);
    exit;
}


if (preg_match('#^/items/(\d+)/(view|download)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardItemController.php';
    $controller = new ClipboardItemController();

    $itemId = $matches[1];
    $action = $matches[2];

    $controller->handleRequest($method, $action, null, $itemId, $userId);
    exit;
}

if (preg_match('#^/items/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardItemController.php';
    $controller = new ClipboardItemController();

    $itemId = $matches[1];
    $controller->handleRequest($method, '', null, $itemId, $userId);
    exit;
}

if (preg_match('#^/clipboards/search/(.+)$#', $path, $matches) && $method === 'GET') {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardController.php';
    $controller = new ClipboardController();

    $controller->handleRequest($method, null, $userId, 'search:' . $matches[1]);
    exit;
}

if (preg_match('#^/clipboards(/(\d+))?(/items)?$#', $path, $matches)) {
    require_once __DIR__ . '/../src/Controllers/Api/ClipboardController.php';
    $controller = new ClipboardController();

    $clipboardId = $matches[2] ?? null;
    $isItems = isset($matches[3]);

    if ($isItems) {
        require_once __DIR__ . '/../src/Controllers/Api/ClipboardItemController.php';
        $itemController = new ClipboardItemController();
        $itemController->handleRequest($method, '', $clipboardId, null, $userId);
    } else {
        $controller->handleRequest($method, $clipboardId, $userId, null);
    }
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);