<?php

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'src/Services/SessionManager.php';
require_once 'src/Middleware/AuthMiddleware.php';
require_once 'src/Controllers/AuthController.php';

SessionManager::initializeSession();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();

switch ($path) {
    case '/':
        if ($method === 'GET') {
            include 'pages/home.php';
        }
        break;
        
    case '/login':
        $authController->login();
        break;
        
    case '/register':
        $authController->register();
        break;
        
    case '/verify-email':
        if ($method === 'GET') {
            $authController->verifyEmail();
        }
        break;
        
    case '/forgot-password':
        $authController->forgotPassword();
        break;
        
    case '/reset-password':
        if ($method === 'GET' || $method === 'POST') {
            $authController->resetPassword();
        }
        break;
        
    case '/logout':
        if ($method === 'GET') {
            $authController->logout();
        }
        break;
        
    case '/dashboard':
        AuthMiddleware::apply(function() {
            include 'pages/dashboard.php';
        });
        break;
        
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
