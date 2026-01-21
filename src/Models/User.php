<?php

class User {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDB();
    }
    
    public function authenticate($email, $password): bool {
        $stmt = $this->pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password_hash']);
    }
    
    public function createAccount($email, $password, $name): int {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Email already exists");
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO users (email, password_hash, name) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $passwordHash, $name]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function isAdmin(): bool {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['is_admin'];
    }
    
    public function getUserById($id): ?array {
        $stmt = $this->pdo->prepare("SELECT id, email, name, is_admin FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function getUserByEmail($email): ?array {
        $stmt = $this->pdo->prepare("SELECT id, email, name, is_admin FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function generatePasswordResetToken($email): bool {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET password_reset_token = ?, password_reset_expires = ? 
            WHERE email = ?
        ");
        $stmt->execute([$token, $expires, $email]);
        
        return true;
    }
    
    public function resetPassword($token, $newPassword): bool {
        $stmt = $this->pdo->prepare("
            SELECT id FROM users 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW()
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL 
            WHERE id = ?
        ");
        $stmt->execute([$passwordHash, $user['id']]);
        
        return true;
    }
    
    public function verifyEmail($token): bool {
        return false;
    }
}
