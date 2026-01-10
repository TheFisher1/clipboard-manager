<?php
/**
 * Cross-Browser Clipboard System - Simple Entry Point
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic configuration
require_once 'config/config.php';

// Simple routing
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Basic routes
if ($path === '/' && $method === 'GET') {
    include 'pages/home.php';
} elseif ($path === '/dashboard' && $method === 'GET') {
    include 'pages/dashboard.php';
} else {
    http_response_code(404);
    echo "Page not found";
}