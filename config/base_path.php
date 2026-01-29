<?php
/**
 * Base Path Configuration
 * Automatically detects the base path for the application
 * Use this in all HTML/PHP files to generate correct URLs
 */

function getBasePath() {
    static $basePath = null;
    
    if ($basePath === null) {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));
        $basePath = rtrim($basePath, '/');
        
        // If we're in a subdirectory like /config, go up one level
        if (basename($basePath) === 'config') {
            $basePath = dirname($basePath);
        }
        
        // If we're in admin or api, go up one level
        if (in_array(basename($basePath), ['admin', 'api', 'public'])) {
            $basePath = dirname($basePath);
        }
        
        $basePath = str_replace('\\', '/', $basePath);
        $basePath = rtrim($basePath, '/');
        
        if ($basePath === '' || $basePath === '.') {
            $basePath = '';
        }
    }
    
    return $basePath;
}

function url($path) {
    $basePath = getBasePath();
    $path = '/' . ltrim($path, '/');
    return $basePath . $path;
}

// Define as constant for easy access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', getBasePath());
}
