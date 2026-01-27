<?php

require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Services/SessionManager.php';

class ApiAuthController
{
    private User $user;

    public function __construct()
    {
        SessionManager::initializeSession();
        $this->user = new User();
    }

    public function handleRequest(string $method, string $action): void
    {
        try {
            switch ($action) {
                case 'login':
                    $method === 'POST' ? $this->login() : $this->sendError('Method not allowed', 405);
                    break;
                case 'register':
                    $method === 'POST' ? $this->register() : $this->sendError('Method not allowed', 405);
                    break;
                case 'logout':
                    $method === 'POST' ? $this->logout() : $this->sendError('Method not allowed', 405);
                    break;
                case 'me':
                    $method === 'GET' ? $this->getCurrentUser() : $this->sendError('Method not allowed', 405);
                    break;
                default:
                    $this->sendError('Endpoint not found', 404);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            $this->sendError('Email and password are required', 400);
            return;
        }

        if ($this->user->authenticate($data['email'], $data['password'])) {
            $userData = $this->user->getUserByEmail($data['email']);
            SessionManager::setUserSession($userData);
            
            SessionManager::logSecurityEvent('successful_login', [
                'user_id' => $userData['id'],
                'email' => $userData['email']
            ]);
            
            $this->sendResponse([
                'message' => 'Login successful',
                'user' => [
                    'id' => $userData['id'],
                    'email' => $userData['email'],
                    'name' => $userData['name'],
                    'is_admin' => $userData['is_admin']
                ]
            ]);
        } else {
            SessionManager::logSecurityEvent('failed_login_attempt', [
                'email' => $data['email']
            ]);
            $this->sendError('Invalid email or password', 401);
        }
    }

    private function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            $this->sendError('Email, password, and name are required', 400);
            return;
        }

        if (strlen($data['password']) < 6) {
            $this->sendError('Password must be at least 6 characters', 400);
            return;
        }

        try {
            $userId = $this->user->createAccount($data['email'], $data['password'], $data['name']);
            
            SessionManager::logSecurityEvent('account_created', [
                'user_id' => $userId,
                'email' => $data['email']
            ]);
            
            $this->sendResponse([
                'message' => 'Account created successfully',
                'user_id' => $userId
            ], 201);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 400);
        }
    }

    private function logout(): void
    {
        $userId = SessionManager::getCurrentUserId();
        
        SessionManager::logSecurityEvent('user_logout', [
            'user_id' => $userId
        ]);
        
        SessionManager::destroySession();
        
        $this->sendResponse(['message' => 'Logged out successfully']);
    }

    private function getCurrentUser(): void
    {
        $user = SessionManager::getCurrentUser();
        
        if (!$user) {
            $this->sendError('Not authenticated', 401);
            return;
        }
        
        $this->sendResponse(['user' => $user]);
    }

    private function sendResponse($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    private function sendError(string $message, int $code = 400): void
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
