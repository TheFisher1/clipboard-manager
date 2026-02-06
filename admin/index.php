<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Services/SessionManager.php';

SessionManager::initializeSession();

// Check if user is authenticated and is admin
if (!SessionManager::isAuthenticated()) {
    header('Location: ' . BASE_PATH . '/public/login.html?redirect=/admin');
    exit;
}

if (!SessionManager::isAdmin()) {
    header('Location: ' . BASE_PATH . '/public/dashboard.html');
    exit;
}

// Redirect to dashboard
header('Location: ' . BASE_PATH . '/admin/dashboard.php');
exit;
