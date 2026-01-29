<?php
/**
 * CSS Loading Test
 * This helps diagnose why CSS files aren't loading
 */

header('Content-Type: text/html; charset=utf-8');

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = str_replace('\\', '/', dirname($scriptName));
$baseDir = rtrim($baseDir, '/');

?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Loading Test</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        .test-section {
            background: #252526;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .test-box {
            width: 100px;
            height: 100px;
            margin: 10px 0;
        }
        a {
            color: #4ec9b0;
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>CSS Loading Test</h1>
    
    <div class="test-section">
        <h2>Path Information</h2>
        <p><strong>Base Directory:</strong> <?php echo htmlspecialchars($baseDir ?: '/'); ?></p>
        <p><strong>Script Name:</strong> <?php echo htmlspecialchars($scriptName); ?></p>
        <p><strong>Request URI:</strong> <?php echo htmlspecialchars($requestUri); ?></p>
    </div>

    <div class="test-section">
        <h2>CSS File Tests</h2>
        <p>Testing if CSS files exist and are accessible:</p>
        
        <?php
        $cssFiles = [
            'public/css/main-styles.css',
            'public/css/nav.css',
            'public/css/buttons.css',
            'public/css/auth.css',
            'admin/css/admin.css'
        ];
        
        foreach ($cssFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            $exists = file_exists($fullPath);
            $readable = $exists && is_readable($fullPath);
            $size = $exists ? filesize($fullPath) : 0;
            
            echo "<p>";
            echo "<strong>$file:</strong> ";
            if ($exists && $readable) {
                echo "<span class='success'>✓ Exists (" . number_format($size) . " bytes)</span>";
            } else if ($exists) {
                echo "<span class='error'>✗ Exists but not readable</span>";
            } else {
                echo "<span class='error'>✗ Not found</span>";
            }
            echo "</p>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2>Direct CSS Links</h2>
        <p>Try accessing these CSS files directly:</p>
        <?php
        foreach ($cssFiles as $file) {
            $url = $baseDir . '/' . $file;
            echo "<a href='$url' target='_blank'>$url</a>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2>Test CSS Loading in HTML</h2>
        <p>This box should be RED if CSS loads correctly:</p>
        <div class="test-box" id="cssTest" style="background: blue;"></div>
        <link rel="stylesheet" href="<?php echo $baseDir; ?>/public/css/main-styles.css">
        <style>
            #cssTest { background: red !important; }
        </style>
    </div>

    <div class="test-section">
        <h2>Test Pages</h2>
        <a href="<?php echo $baseDir; ?>/">Home Page</a>
        <a href="<?php echo $baseDir; ?>/public/login.html">Login Page</a>
        <a href="<?php echo $baseDir; ?>/admin/dashboard.php">Admin Dashboard</a>
    </div>

    <div class="test-section">
        <h2>Browser Console Check</h2>
        <p>Open your browser's Developer Tools (F12) and check the Console and Network tabs for errors.</p>
        <p>Look for 404 errors or MIME type warnings.</p>
    </div>

    <script>
        console.log('Base Path:', '<?php echo $baseDir; ?>');
        console.log('Testing CSS file access...');
        
        // Test if we can fetch a CSS file
        fetch('<?php echo $baseDir; ?>/public/css/main-styles.css')
            .then(response => {
                console.log('CSS fetch status:', response.status);
                console.log('CSS content-type:', response.headers.get('content-type'));
                return response.text();
            })
            .then(text => {
                console.log('CSS content length:', text.length);
                console.log('CSS preview:', text.substring(0, 200));
            })
            .catch(error => {
                console.error('CSS fetch error:', error);
            });
    </script>

</body>
</html>
