<?php

require_once 'src/Models/User.php';
require_once 'src/Services/SessionManager.php';
require_once 'src/Middleware/AuthMiddleware.php';

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    public function login() {
        if (!AuthMiddleware::checkRateLimit('login', 5, 300)) {
            $error = "Too many login attempts. Please try again later.";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthMiddleware::checkCSRF()) {
                $error = "Invalid security token. Please try again.";
            } else {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    $error = "Email and password are required";
                } elseif ($this->user->authenticate($email, $password)) {
                    $userData = $this->user->getUserByEmail($email);
                    SessionManager::setUserSession($userData);
                    
                    SessionManager::logSecurityEvent('successful_login', [
                        'user_id' => $userData['id'],
                        'email' => $userData['email']
                    ]);
                    
                    $redirectUrl = SessionManager::getIntendedUrl();
                    header("Location: $redirectUrl");
                    exit;
                } else {
                    $error = "Invalid email or password, or email not verified";
                    SessionManager::logSecurityEvent('failed_login_attempt', [
                        'email' => $email
                    ]);
                }
            }
        }
        
        $csrfToken = AuthMiddleware::generateCSRFToken();
        
        // Show login form
        include 'views/auth/login.php';
    }
    
    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check CSRF token
            if (!AuthMiddleware::checkCSRF()) {
                $errors[] = "Invalid security token. Please try again.";
            } else {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $name = $_POST['name'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                $errors = [];
                
                if (empty($email)) $errors[] = "Email is required";
                if (empty($password)) $errors[] = "Password is required";
                if (empty($name)) $errors[] = "Name is required";
                if ($password !== $confirmPassword) $errors[] = "Passwords do not match";
                if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
                
                if (empty($errors)) {
                    try {
                        $userId = $this->user->createAccount($email, $password, $name);
                        $success = "Account created successfully! You can now log in.";
                        
                        SessionManager::logSecurityEvent('account_created', [
                            'user_id' => $userId,
                            'email' => $email
                        ]);
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }
        
        $csrfToken = AuthMiddleware::generateCSRFToken();
        
        include 'views/auth/register.php';
    }
    
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $error = "Invalid verification token";
        } elseif ($this->user->verifyEmail($token)) {
            $success = "Email verified successfully! You can now log in.";
            SessionManager::logSecurityEvent('email_verified', [
                'token' => substr($token, 0, 8) . '...'
            ]);
        } else {
            $error = "Invalid or expired verification token";
        }
        
        include 'views/auth/verify-email.php';
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check rate limiting for password reset
            if (!AuthMiddleware::checkRateLimit('password_reset', 3, 300)) {
                $error = "Too many password reset attempts. Please try again later.";
            } elseif (!AuthMiddleware::checkCSRF()) {
                $error = "Invalid security token. Please try again.";
            } else {
                $email = $_POST['email'] ?? '';
                
                if (empty($email)) {
                    $error = "Email is required";
                } elseif ($this->user->generatePasswordResetToken($email)) {
                    $success = "Password reset link sent to your email";
                    SessionManager::logSecurityEvent('password_reset_requested', [
                        'email' => $email
                    ]);
                } else {
                    $error = "Email not found";
                }
            }
        }
        
        $csrfToken = AuthMiddleware::generateCSRFToken();
        
        include 'views/auth/forgot-password.php';
    }
    
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthMiddleware::checkCSRF()) {
                $error = "Invalid security token. Please try again.";
            } else {
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($password)) {
                    $error = "Password is required";
                } elseif ($password !== $confirmPassword) {
                    $error = "Passwords do not match";
                } elseif (strlen($password) < 6) {
                    $error = "Password must be at least 6 characters";
                } elseif ($this->user->resetPassword($token, $password)) {
                    $success = "Password reset successfully! You can now log in.";
                    SessionManager::logSecurityEvent('password_reset_completed', [
                        'token' => substr($token, 0, 8) . '...'
                    ]);
                } else {
                    $error = "Invalid or expired reset token";
                }
            }
        }
        
        $csrfToken = AuthMiddleware::generateCSRFToken();
        
        include 'views/auth/reset-password.php';
    }
    
    public function logout() {
        $userId = SessionManager::getCurrentUserId();
        
        SessionManager::logSecurityEvent('user_logout', [
            'user_id' => $userId
        ]);
        
        SessionManager::destroySession();
        header('Location: /');
        exit;
    }
}