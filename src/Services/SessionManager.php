<?php

class SessionManager {
    private const SESSION_TIMEOUT = 60 * 60;
    private const ACTIVITY_TIMEOUT = 30 * 60;
    
    public static function initializeSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', self::SESSION_TIMEOUT);
            
            session_start();
        }
        
        self::validateSession();
    }
    
    private static function validateSession(): void {
        $currentTime = time();
        
        if (isset($_SESSION['created_at'])) {
            if ($currentTime - $_SESSION['created_at'] > self::SESSION_TIMEOUT) {
                self::destroySession();
                return;
            }
        } else {
            $_SESSION['created_at'] = $currentTime;
        }
        
        if (isset($_SESSION['last_activity'])) {
            if ($currentTime - $_SESSION['last_activity'] > self::ACTIVITY_TIMEOUT) {
                self::destroySession();
                return;
            }
        }
        
        $_SESSION['last_activity'] = $currentTime;
        
        if (!isset($_SESSION['regenerated_at']) || 
            $currentTime - $_SESSION['regenerated_at'] > 300) {
            session_regenerate_id(true);
            $_SESSION['regenerated_at'] = $currentTime;
        }
    }
    
    public static function isAuthenticated(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function isAdmin(): bool {
        return self::isAuthenticated() && 
               isset($_SESSION['is_admin']) && 
               $_SESSION['is_admin'] === true;
    }
    
    public static function getCurrentUserId(): ?int {
        return self::isAuthenticated() ? (int)$_SESSION['user_id'] : null;
    }
    
    public static function getCurrentUser(): ?array {
        if (!self::isAuthenticated()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? '',
            'name' => $_SESSION['user_name'] ?? '',
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }
    
    public static function setUserSession($userData): void {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['is_admin'] = $userData['is_admin'];
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();
        
        session_regenerate_id(true);
    }
    
    public static function destroySession(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    public static function requireAuth($redirectTo = '/login'): void {
        if (!self::isAuthenticated()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header("Location: $redirectTo");
            exit;
        }
    }
    
    public static function requireAdmin($redirectTo = '/dashboard'): void {
        self::requireAuth();
        
        if (!self::isAdmin()) {
            header("Location: $redirectTo");
            exit;
        }
    }
    
    public static function getIntendedUrl(): string {
        $url = $_SESSION['intended_url'] ?? '/dashboard';
        unset($_SESSION['intended_url']);
        return $url;
    }
    
    public static function canAccessClipboard($clipboardId): bool {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        if (self::isAdmin()) {
            return true;
        }
        
        return true;
    }
    
    public static function logSecurityEvent($event, $details = []): void {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'user_id' => self::getCurrentUserId(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        error_log('SECURITY: ' . json_encode($logData));
    }
}
