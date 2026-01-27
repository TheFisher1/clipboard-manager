CREATE DATABASE IF NOT EXISTS clipboard_system;
USE clipboard_system;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clipboard groups for organization
CREATE TABLE clipboard_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Clipboards configuration
CREATE TABLE clipboards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    owner_id INT NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    max_subscribers INT DEFAULT NULL, -- NULL = unlimited, 1 = single subscriber
    max_items INT DEFAULT NULL, -- NULL = unlimited, 1 = single item
    allowed_content_types JSON, -- Array of MIME types
    default_expiration_minutes INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table that links groups and clipboards
CREATE TABLE clipboard_group_map (
    clipboard_id INT NOT NULL,
    group_id INT NOT NULL,
    PRIMARY KEY (clipboard_id, group_id),

    CONSTRAINT fk_map_clipboard
        FOREIGN KEY (clipboard_id)
        REFERENCES clipboards(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_map_group
        FOREIGN KEY (group_id)
        REFERENCES clipboard_groups(id)
        ON DELETE CASCADE
);

-- Clipboard subscriptions
CREATE TABLE clipboard_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clipboard_id INT NOT NULL,
    user_id INT NOT NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_subscription (clipboard_id, user_id),
    FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Shared content/resources
CREATE TABLE clipboard_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clipboard_id INT NOT NULL,
    content_type VARCHAR(100) NOT NULL, -- MIME type
    content_text TEXT NULL, -- For text/code content
    file_path VARCHAR(500) NULL, -- For uploaded files
    original_filename VARCHAR(255) NULL,
    file_size INT NULL,
    url VARCHAR(1000) NULL, -- For shared links
    title VARCHAR(255) NULL, -- Link title or custom title
    description TEXT NULL,
    submitted_by INT NOT NULL,
    expires_at TIMESTAMP NULL,
    view_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    is_single_use BOOLEAN DEFAULT FALSE,
    is_consumed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE clipboard_activity (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clipboard_id INT NOT NULL,
    item_id INT NULL,
    user_id INT NOT NULL,
    action_type ENUM('create', 'view', 'download', 'delete', 'expire', 'share') NOT NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES clipboard_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE api_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    permissions JSON,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE admin_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_user_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    target_id INT NULL,
    action_details JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin_user (admin_user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_target (target_type, target_id),
    INDEX idx_created_at (created_at)
);

CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    setting_type VARCHAR(20) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_setting_key (setting_key)
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_is_admin ON users(is_admin);
CREATE INDEX idx_clipboards_owner ON clipboards(owner_id);
CREATE INDEX idx_clipboards_public ON clipboards(is_public);
CREATE INDEX idx_clipboard_items_clipboard ON clipboard_items(clipboard_id);
CREATE INDEX idx_clipboard_items_submitted ON clipboard_items(submitted_by);
CREATE INDEX idx_clipboard_items_expires ON clipboard_items(expires_at);
CREATE INDEX idx_clipboard_activity_user ON clipboard_activity(user_id);
CREATE INDEX idx_clipboard_activity_clipboard ON clipboard_activity(clipboard_id);
CREATE INDEX idx_clipboard_activity_created ON clipboard_activity(created_at);

INSERT INTO users (email, password_hash, name, is_admin) VALUES 
('admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', TRUE);
