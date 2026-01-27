<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Services/SessionManager.php';

SessionManager::initializeSession();

// Check if user is authenticated and is admin
if (!SessionManager::isAuthenticated()) {
    header('Location: /public/login.html?redirect=/admin');
    exit;
}

if (!SessionManager::isAdmin()) {
    header('Location: /public/dashboard.html');
    exit;
}

// Redirect to dashboard
header('Location: /admin/dashboard.html');
exit;
