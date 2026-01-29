<?php
/**
 * Simple routing test script
 * This helps verify that URL rewriting is working correctly
 */

header('Content-Type: text/html; charset=utf-8');

$requestUri = $_SERVER['REQUEST_URI'] ?? 'Not set';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? 'Not set';
$pathInfo = $_SERVER['PATH_INFO'] ?? 'Not set';
$queryString = $_SERVER['QUERY_STRING'] ?? 'Not set';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Routing Test</title>
    <style>
        body {
            font-family: monospace;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .info {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        h1 {
            color: #333;
        }
        .test-links {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .test-links a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .test-links a:hover {
            background: #1976D2;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>🔍 Routing Test</h1>
    
    <div class="info">
        <strong>REQUEST_URI:</strong> <?php echo htmlspecialchars($requestUri); ?>
    </div>
    
    <div class="info">
        <strong>SCRIPT_NAME:</strong> <?php echo htmlspecialchars($scriptName); ?>
    </div>
    
    <div class="info">
        <strong>PATH_INFO:</strong> <?php echo htmlspecialchars($pathInfo); ?>
    </div>
    
    <div class="info">
        <strong>QUERY_STRING:</strong> <?php echo htmlspecialchars($queryString); ?>
    </div>

    <div class="test-links">
        <h2>Test These Links:</h2>
        <p>Click these links to test if routing is working correctly:</p>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/" target="_blank">
            Home Page (should show public/index.html)
        </a>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/login.html" target="_blank">
            Login Page
        </a>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/api/auth/me" target="_blank">
            API Test (should return JSON - might show auth error)
        </a>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/" target="_blank">
            Admin Panel
        </a>
        
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/check_config.php" target="_blank">
            Configuration Check
        </a>
    </div>

    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <strong>Expected Behavior:</strong>
        <ul>
            <li>Home page should load without showing "index.php" in URL</li>
            <li>API endpoints should return JSON responses</li>
            <li>Static files (CSS, JS, images) should load correctly</li>
            <li>Admin panel should be accessible</li>
        </ul>
        
        <strong>If links don't work:</strong>
        <ul>
            <li>Make sure mod_rewrite is enabled in Apache</li>
            <li>Check that .htaccess file exists in the root directory</li>
            <li>Verify AllowOverride is set to "All" in httpd.conf</li>
            <li>Restart Apache after making changes</li>
        </ul>
    </div>

</body>
</html>
