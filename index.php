<?php
// Get the request URI and remove query string
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove script name from path if present (for XAMPP compatibility)
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

// Ensure path starts with /
$path = '/' . ltrim($requestUri, '/');

// API routes should be handled by their respective index.php files
// This is a fallback in case .htaccess doesn't catch them
if (strpos($path, '/api/admin') === 0) {
    require_once __DIR__ . '/api/admin/index.php';
    exit;
}

if (strpos($path, '/api') === 0) {
    require_once __DIR__ . '/api/index.php';
    exit;
}

// Admin panel routes
if (strpos($path, '/admin') === 0) {
    $adminFile = __DIR__ . $path;
    if (file_exists($adminFile) && is_file($adminFile)) {
        // Serve the admin file directly
        $ext = pathinfo($adminFile, PATHINFO_EXTENSION);
        if ($ext === 'php') {
            require_once $adminFile;
            exit;
        }
    }
}

// Public files
$publicPath = __DIR__ . '/public';

// Default to index.html for root
if ($path === '/' || $path === '') {
    $path = '/index.html';
}

// Try to serve from public directory
$filePath = $publicPath . $path;

// Security check: ensure the real path is within public directory
$realPath = realpath($filePath);
$realPublicPath = realpath($publicPath);

if ($realPath !== false && strpos($realPath, $realPublicPath) === 0) {
    if (file_exists($filePath) && is_file($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf'
        ];
        
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        readfile($filePath);
        exit;
    }
}

// 404 - Not found
http_response_code(404);
header('Content-Type: text/html');
echo '<!DOCTYPE html>
<html>
<head>
    <title>404 - Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The requested resource could not be found.</p>
    <p><a href="/">Go to Home</a></p>
</body>
</html>';
