<?php
/**
 * Debug page to help diagnose XAMPP routing issues
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Information</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        h1, h2 {
            color: #4ec9b0;
        }
        .section {
            background: #252526;
            padding: 20px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #007acc;
        }
        .key {
            color: #9cdcfe;
            font-weight: bold;
        }
        .value {
            color: #ce9178;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #3e3e42;
        }
        th {
            background: #2d2d30;
            color: #4ec9b0;
        }
        .success {
            color: #4ec9b0;
        }
        .error {
            color: #f48771;
        }
        .warning {
            color: #dcdcaa;
        }
        code {
            background: #1e1e1e;
            padding: 2px 6px;
            border-radius: 3px;
            color: #ce9178;
        }
        .test-button {
            background: #007acc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .test-button:hover {
            background: #005a9e;
        }
    </style>
</head>
<body>
    <h1>🔍 XAMPP Debug Information</h1>

    <div class="section">
        <h2>Server Variables</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
            </tr>
            <?php
            $importantVars = [
                'SERVER_SOFTWARE',
                'SERVER_NAME',
                'SERVER_ADDR',
                'SERVER_PORT',
                'DOCUMENT_ROOT',
                'SCRIPT_FILENAME',
                'SCRIPT_NAME',
                'PHP_SELF',
                'REQUEST_URI',
                'REQUEST_METHOD',
                'QUERY_STRING',
                'HTTP_HOST',
                'HTTP_USER_AGENT'
            ];
            
            foreach ($importantVars as $var) {
                $value = $_SERVER[$var] ?? '<span class="error">Not Set</span>';
                echo "<tr><td class='key'>$var</td><td class='value'>" . htmlspecialchars($value) . "</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h2>PHP Configuration</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td class="key">PHP Version</td>
                <td class="value"><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td class="key">PHP SAPI</td>
                <td class="value"><?php echo php_sapi_name(); ?></td>
            </tr>
            <tr>
                <td class="key">Loaded Extensions</td>
                <td class="value"><?php echo implode(', ', get_loaded_extensions()); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Apache Modules (if available)</h2>
        <?php
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $hasRewrite = in_array('mod_rewrite', $modules);
            echo "<p><span class='key'>mod_rewrite:</span> ";
            echo $hasRewrite ? "<span class='success'>✓ Enabled</span>" : "<span class='error'>✗ Not Enabled</span>";
            echo "</p>";
            echo "<details><summary>All Modules (" . count($modules) . ")</summary>";
            echo "<p>" . implode(', ', $modules) . "</p>";
            echo "</details>";
        } else {
            echo "<p class='warning'>apache_get_modules() not available (might be running under CGI/FastCGI)</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>File System Checks</h2>
        <table>
            <tr>
                <th>Check</th>
                <th>Status</th>
            </tr>
            <?php
            $checks = [
                '.htaccess' => file_exists(__DIR__ . '/.htaccess'),
                'config/config.php' => file_exists(__DIR__ . '/config/config.php'),
                'api/index.php' => file_exists(__DIR__ . '/api/index.php'),
                'api/admin/index.php' => file_exists(__DIR__ . '/api/admin/index.php'),
                'public/index.html' => file_exists(__DIR__ . '/public/index.html'),
                'uploads/ (writable)' => is_writable(__DIR__ . '/uploads')
            ];
            
            foreach ($checks as $file => $exists) {
                $status = $exists ? "<span class='success'>✓ OK</span>" : "<span class='error'>✗ Missing/Not Writable</span>";
                echo "<tr><td class='key'>$file</td><td>$status</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
        <h2>Database Connection</h2>
        <?php
        try {
            require_once __DIR__ . '/config/config.php';
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p class='success'>✓ Database connection successful</p>";
            echo "<p><span class='key'>Host:</span> <span class='value'>" . DB_HOST . "</span></p>";
            echo "<p><span class='key'>Database:</span> <span class='value'>" . DB_NAME . "</span></p>";
            echo "<p><span class='key'>User:</span> <span class='value'>" . DB_USER . "</span></p>";
        } catch (PDOException $e) {
            echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>Path Analysis</h2>
        <?php
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $path = parse_url($requestUri, PHP_URL_PATH);
        $baseDir = rtrim(dirname($scriptName), '/');
        
        echo "<p><span class='key'>Request URI:</span> <code>" . htmlspecialchars($requestUri) . "</code></p>";
        echo "<p><span class='key'>Script Name:</span> <code>" . htmlspecialchars($scriptName) . "</code></p>";
        echo "<p><span class='key'>Parsed Path:</span> <code>" . htmlspecialchars($path) . "</code></p>";
        echo "<p><span class='key'>Base Directory:</span> <code>" . htmlspecialchars($baseDir ?: '/') . "</code></p>";
        
        if ($baseDir && $baseDir !== '/') {
            $cleanPath = substr($path, strlen($baseDir));
            echo "<p><span class='key'>Clean Path (after removing base):</span> <code>" . htmlspecialchars($cleanPath) . "</code></p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>Test Links</h2>
        <p>Click these links to test routing:</p>
        <?php
        $base = $baseDir ?: '';
        $tests = [
            'Home Page' => $base . '/',
            'Login Page' => $base . '/public/login.html',
            'Register Page' => $base . '/public/register.html',
            'API Test (auth/me)' => $base . '/api/auth/me',
            'Install Page' => $base . '/install.php',
            'Config Check' => $base . '/check_config.php',
            'Test Routing' => $base . '/test_routing.php'
        ];
        
        foreach ($tests as $name => $url) {
            echo "<a href='$url' class='test-button' target='_blank'>$name</a>";
        }
        ?>
    </div>

    <div class="section">
        <h2>AJAX API Test</h2>
        <button class="test-button" onclick="testAPI()">Test API Endpoint</button>
        <div id="api-result" style="margin-top: 10px;"></div>
        
        <script>
        async function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<p class="warning">Testing...</p>';
            
            try {
                const response = await fetch('<?php echo $base; ?>/api/auth/me', {
                    credentials: 'include'
                });
                const data = await response.json();
                
                resultDiv.innerHTML = `
                    <p class="success">✓ API is responding</p>
                    <p><span class="key">Status:</span> ${response.status}</p>
                    <p><span class="key">Response:</span></p>
                    <pre style="background: #1e1e1e; padding: 10px; border-radius: 3px; overflow-x: auto;">${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<p class="error">✗ API test failed: ${error.message}</p>`;
            }
        }
        </script>
    </div>

    <div class="section">
        <h2>Recommendations</h2>
        <?php
        $issues = [];
        
        if (!file_exists(__DIR__ . '/.htaccess')) {
            $issues[] = "❌ .htaccess file is missing - URL rewriting won't work";
        }
        
        if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {
            $issues[] = "❌ mod_rewrite is not enabled - enable it in httpd.conf";
        }
        
        if (!is_writable(__DIR__ . '/uploads')) {
            $issues[] = "⚠️ uploads directory is not writable - file uploads will fail";
        }
        
        try {
            require_once __DIR__ . '/config/config.php';
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            $issues[] = "❌ Database connection failed - check config/config.php";
        }
        
        if (empty($issues)) {
            echo "<p class='success'>✓ No critical issues detected! Your setup looks good.</p>";
            echo "<p>If you're still having problems, check:</p>";
            echo "<ul>";
            echo "<li>Make sure AllowOverride is set to 'All' in httpd.conf</li>";
            echo "<li>Restart Apache after making configuration changes</li>";
            echo "<li>Check Apache error logs for specific errors</li>";
            echo "</ul>";
        } else {
            echo "<p class='error'>Found " . count($issues) . " issue(s):</p>";
            echo "<ul>";
            foreach ($issues as $issue) {
                echo "<li>$issue</li>";
            }
            echo "</ul>";
        }
        ?>
    </div>

</body>
</html>
