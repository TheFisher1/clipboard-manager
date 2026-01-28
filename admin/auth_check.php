<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Services/SessionManager.php';

SessionManager::initializeSession();

// Check if user is authenticated
if (!SessionManager::isAuthenticated()) {
    header('Location: /public/login.html?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Check if user is admin
if (!SessionManager::isAdmin()) {
    header('Location: /public/dashboard.html');
    exit;
}
