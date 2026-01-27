<?php

class EmailService {
    
    public function sendVerificationEmail($email, $token): bool {
        return true;
    }
    
    public function sendPasswordResetEmail($email, $token): bool {
        return true;
    }
}
