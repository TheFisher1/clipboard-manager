-- Simple Database Schema
CREATE DATABASE IF NOT EXISTS clipboard_system;
USE clipboard_system;

-- Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clipboards
CREATE TABLE clipboards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    owner_id INT NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Content Items
CREATE TABLE clipboard_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clipboard_id INT NOT NULL,
    content_type VARCHAR(100) NOT NULL,
    content_text TEXT,
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clipboard_id) REFERENCES clipboards(id)
);

-- Default admin user (password: admin123)
INSERT INTO users (email, password_hash, name) VALUES 
('admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');