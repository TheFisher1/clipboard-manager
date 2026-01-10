<?php
/**
 * Basic Configuration
 */

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'clipboard_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// App
define('APP_NAME', 'Clipboard System');

// Simple database connection function
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}