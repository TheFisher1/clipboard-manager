<?php
/**
 * Main Router for XAMPP
 * Handles all incoming requests and routes them appropriately
 */

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Parse the URI to get the path without query string
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Get the base directory (in case app is in a subdirectory)
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = str_replace('\\', '/', dirname($scriptName));
$baseDir = rtrim($baseDir, '/');

// Remove base directory from path if present
if ($baseDir && $baseDir !== '/' && strpos($path, $baseDir) === 0) {
    $path = substr($path, strlen($baseDir));
}

// Ensure path starts with /
$path = '/' . ltrim($path, '/');

// Remove trailing slash except for root
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// ============================================
// ROUTE: Admin API
// ============================================
if (strpos($path, '/api/admin') === 0) {
    require_once __DIR__ . '/api/admin/index.php';
    exit;
}

// ============================================
// ROUTE: Regular API
// ============================================
if (strpos($path, '/api') === 0) {
    require_once __DIR__ . '/api/index.php';
    exit;
}

// ============================================
// ROUTE: Admin Panel PHP Files
// ============================================
if (strpos($path, '/admin/') === 0) {
    $adminFile = __DIR__ . $path;
    
    // Check if it's a PHP file
    if (file_exists($adminFile) && is_file($adminFile)) {
        $ext = pathinfo($adminFile, PATHINFO_EXTENSION);
        
        if ($ext === 'php') {
            require_once $adminFile;
            exit;
        }
        
        // Serve other admin files (CSS, JS, images)
        serveStaticFile($adminFile);
        exit;
    }
    
    // If admin/index.php exists and path is just /admin or /admin/
    if (($path === '/admin' || $path === '/admin/') && file_exists(__DIR__ . '/admin/index.php')) {
        require_once __DIR__ . '/admin/index.php';
        exit;
    }
}

// ============================================
// ROUTE: Special PHP Files in Root
// ============================================
$specialFiles = ['install.php', 'check_config.php', 'debug.php', 'test_routing.php'];
foreach ($specialFiles as $file) {
    if ($path === '/' . $file) {
        $filePath = __DIR__ . '/' . $file;
        if (file_exists($filePath)) {
            require_once $filePath;
            exit;
        }
    }
}

// ============================================
// ROUTE: Public Files
// ============================================
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

if ($realPath !== false && $realPublicPath !== false && strpos($realPath, $realPublicPath) === 0) {
    if (is_file($filePath)) {
        serveStaticFile($filePath);
        exit;
    }
}

// ============================================
// 404 - Not Found
// ============================================
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>404 - Not Found</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 600px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            color: #333;
        }
        h1 {
            color: #667eea;
            font-size: 72px;
            margin: 0;
            font-weight: bold;
        }
        h2 {
            color: #333;
            margin: 10px 0 20px 0;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin: 15px 0;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s;
        }
        a:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .debug {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 12px;
            text-align: left;
            font-family: 'Courier New', monospace;
        }
        .debug-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?php echo $baseDir ?: '/'; ?>">← Back to Home</a>
        
        <div class="debug">
            <div class="debug-title">Debug Information:</div>
            <strong>Requested:</strong> <?php echo htmlspecialchars($path); ?><br>
            <strong>Base Dir:</strong> <?php echo htmlspecialchars($baseDir ?: '/'); ?><br>
            <strong>Method:</strong> <?php echo htmlspecialchars($requestMethod); ?>
        </div>
    </div>
</body>
</html>
<?php
exit;

/**
 * Helper function to serve static files with proper MIME types
 */
function serveStaticFile($filePath) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        // Text
        'html' => 'text/html',
        'htm' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'txt' => 'text/plain',
        
        // Images
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        
        // Fonts
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'eot' => 'application/vnd.ms-fontobject',
        
        // Other
        'pdf' => 'application/pdf',
        'zip' => 'application/zip'
    ];
    
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    
    // Cache headers for static assets
    if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf'])) {
        header('Cache-Control: public, max-age=86400'); // 1 day
    }
    
    readfile($filePath);
}
