<?php
require_once __DIR__ . '/../../Models/User.php';

class UserController {
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function getById(int $id) {
        $to_return = $this->user->getUserById($id);

        if ($to_return === null) {
            $this->sendError('USER_NOT_FOUND', 404);
        }
        
        $this->sendResponse($this->userToArray($to_return), 200);
    }

    private function userToArray(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'is_admin' => (bool) $user['is_admin']
        ];
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