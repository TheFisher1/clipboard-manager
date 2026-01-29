<?php

// Normalize the path by removing the subdirectory (XAMPP fix)
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the URI starts with the basePath, remove it
if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
} else {
    $path = $requestUri;
}

// Ensure $path starts with / and isn't empty
$path = '/' . ltrim($path, '/');

// --- NOW YOUR ORIGINAL LOGIC WORKS ---
if (strpos($path, '/api') === 0) {
    require_once 'api/index.php';
    exit;
}

$publicPath = __DIR__ . '/public';
if ($path === '/') { $path = '/index.html'; }

$filePath = $publicPath . $path;
// ... rest of your readfile logic

$realPath = realpath($filePath);
if ($realPath === false || strpos($realPath, $publicPath) !== 0) {
    http_response_code(404);
    echo "Not found";
    exit;
}

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
        'svg' => 'image/svg+xml'
    ];
    
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $mimeType);
    readfile($filePath);
    exit;
}

http_response_code(404);
echo "Not found";
