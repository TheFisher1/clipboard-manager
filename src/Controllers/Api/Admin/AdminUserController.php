<?php

require_once __DIR__ . '/../../../Core/Repository/AdminRepository.php';
require_once __DIR__ . '/../../../Services/AdminAuditService.php';
require_once __DIR__ . '/../../../Services/SessionManager.php';
require_once __DIR__ . '/../../../Helpers/Response.php';

class AdminUserController {
    private $repository;
    private $auditService;

    public function __construct() {
        $this->repository = new AdminRepository();
        $this->auditService = new AdminAuditService();
    }

    public function handleRequest($method, $userId = null, $action = null) {
        switch ($method) {
            case 'GET':
                if ($userId) {
                    $this->getUserDetail($userId);
                } else {
                    $this->listUsers();
                }
                break;
            case 'PUT':
                if ($userId) {
                    $this->updateUser($userId);
                }
                break;
            case 'DELETE':
                if ($userId) {
                    $this->deleteUser($userId);
                }
                break;
            case 'POST':
                if ($action === 'reset-password' && $userId) {
                    $this->resetPassword($userId);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'METHOD_NOT_ALLOWED',
                        'message' => 'Method not allowed'
                    ]
                ]);
                exit;
        }
    }

    /**
     * GET /api/admin/users - List users with pagination and filtering
     */
    private function listUsers() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;
        $search = $_GET['search'] ?? null;
        
        $filters = [];
        if (isset($_GET['is_admin'])) {
            $filters['is_admin'] = $_GET['is_admin'];
        }
        if (isset($_GET['email_verified'])) {
            $filters['email_verified'] = $_GET['email_verified'];
        }
        if (isset($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }
        if (isset($_GET['order'])) {
            $filters['order'] = $_GET['order'];
        }

        $result = $this->repository->getAllUsers($page, $perPage, $search, $filters);
        Response::success($result);
    }

    /**
     * GET /api/admin/users/:id - Get user detail with statistics
     */
    private function getUserDetail($userId) {
        $user = $this->repository->getUserStats($userId);
        
        if (empty($user)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ]);
            exit;
        }

        Response::success($user);
    }

    /**
     * PUT /api/admin/users/:id - Update user information
     */
    private function updateUser($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_DATA',
                    'message' => 'Invalid request data'
                ]
            ]);
            exit;
        }

        // Validate email if provided
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_EMAIL',
                    'message' => 'Invalid email format'
                ]
            ]);
            exit;
        }

        // Get current user data for audit log
        $oldUser = $this->repository->getUserById($userId);
        if (!$oldUser) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ]);
            exit;
        }

        $success = $this->repository->updateUser($userId, $data);
        
        if ($success) {
            // Log the action
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'user_update',
                'user',
                $userId,
                [
                    'updated_fields' => array_keys($data),
                    'old_values' => array_intersect_key($oldUser, $data),
                    'new_values' => $data
                ]
            );

            $updatedUser = $this->repository->getUserById($userId);
            Response::success([
                'message' => 'User updated successfully',
                'user' => $updatedUser
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'UPDATE_FAILED',
                    'message' => 'Failed to update user'
                ]
            ]);
            exit;
        }
    }

    /**
     * DELETE /api/admin/users/:id - Delete user
     */
    private function deleteUser($userId) {
        // Prevent deleting yourself
        if ($userId == SessionManager::getCurrentUserId()) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'CANNOT_DELETE_SELF',
                    'message' => 'Cannot delete your own account'
                ]
            ]);
            exit;
        }

        // Get user data before deletion for audit log
        $user = $this->repository->getUserById($userId);
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ]);
            exit;
        }

        $success = $this->repository->deleteUser($userId);
        
        if ($success) {
            // Log the action
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'user_delete',
                'user',
                $userId,
                [
                    'deleted_user' => $user
                ]
            );

            Response::success([
                'message' => 'User deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'DELETE_FAILED',
                    'message' => 'Failed to delete user'
                ]
            ]);
            exit;
        }
    }

    /**
     * POST /api/admin/users/:id/reset-password - Reset user password
     */
    private function resetPassword($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['new_password']) || strlen($data['new_password']) < 8) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_PASSWORD',
                    'message' => 'Password must be at least 8 characters'
                ]
            ]);
            exit;
        }

        $user = $this->repository->getUserById($userId);
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ]);
            exit;
        }

        $success = $this->repository->resetUserPassword($userId, $data['new_password']);
        
        if ($success) {
            // Log the action
            $this->auditService->logAction(
                SessionManager::getCurrentUserId(),
                'user_password_reset',
                'user',
                $userId,
                [
                    'reset_by_admin' => true
                ]
            );

            Response::success([
                'message' => 'Password reset successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'RESET_FAILED',
                    'message' => 'Failed to reset password'
                ]
            ]);
            exit;
        }
    }
}
