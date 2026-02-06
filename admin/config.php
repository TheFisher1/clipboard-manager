<?php
// Detect the base path for the application
function getBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    // Extract base path from script name
    // e.g., /clipboard_manager/admin/dashboard.php -> /clipboard_manager
    if (preg_match('#^(/[^/]+)/admin/#', $scriptName, $matches)) {
        return $matches[1];
    }
    return '';
}

$basePath = getBasePath();
define('BASE_PATH', $basePath);
