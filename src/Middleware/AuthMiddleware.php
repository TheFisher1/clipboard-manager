<?php

require_once 'src/Services/SessionManager.php';

class AuthMiddleware {
    
    public static function checkAuth(): bool {
        SessionManager::initializeSession();
        
        if (!SessionManager::isAuthenticated()) {
            SessionManager::logSecurityEvent('unauthorized_access_attempt', [
                'url' => $_SERVER['REQUEST_URI'],
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            return false;
        }
        
        return true;
    }
    
    public static function checkAdmin(): bool {
        if (!self::checkAuth()) {
            return false;
        }
        
        if (!SessionManager::isAdmin()) {
            SessionManager::logSecurityEvent('admin_access_denied', [
                'user_id' => SessionManager::getCurrentUserId(),
                'url' => $_SERVER['REQUEST_URI']
            ]);
            return false;
        }
        
        return true;
    }

    public static function apply($callback, $requireAdmin = false) {
        if ($requireAdmin) {
            if (!self::checkAdmin()) {
                SessionManager::requireAdmin();
                return;
            }
        } else {
            if (!self::checkAuth()) {
                SessionManager::requireAuth();
                return;
            }
        }
        
        call_user_func($callback);
    }
    
    public static function checkCSRF(): bool {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';
            
            if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
                SessionManager::logSecurityEvent('csrf_token_mismatch', [
                    'url' => $_SERVER['REQUEST_URI'],
                    'user_id' => SessionManager::getCurrentUserId()
                ]);
                return false;
            }
        }
        
        return true;
    }
    
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function checkRateLimit($action = 'general', $maxAttempts = 10, $timeWindow = 300): bool {
        $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION['rate_limits'])) {
            $_SESSION['rate_limits'] = [];
        }
        
        $now = time();
        
        foreach ($_SESSION['rate_limits'] as $k => $data) {
            if ($now - $data['first_attempt'] > $timeWindow) {
                unset($_SESSION['rate_limits'][$k]);
            }
        }
        
        if (!isset($_SESSION['rate_limits'][$key])) {
            $_SESSION['rate_limits'][$key] = [
                'count' => 1,
                'first_attempt' => $now
            ];
            return true;
        }
        
        $rateData = $_SESSION['rate_limits'][$key];
        
        if ($now - $rateData['first_attempt'] > $timeWindow) {
            $_SESSION['rate_limits'][$key] = [
                'count' => 1,
                'first_attempt' => $now
            ];
            return true;
        }
        
        if ($rateData['count'] >= $maxAttempts) {
            SessionManager::logSecurityEvent('rate_limit_exceeded', [
                'action' => $action,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'attempts' => $rateData['count']
            ]);
            return false;
        }
        
        $_SESSION['rate_limits'][$key]['count']++;
        return true;
    }
}