<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/api') === 0) {
    require_once 'api/index.php';
    exit;
}

$publicPath = __DIR__ . '/public';

if ($path === '/') {
    $path = '/index.html';
}

$filePath = $publicPath . $path;

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
