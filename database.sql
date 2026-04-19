-- ============================================================
-- Hash & Password Security Website — Database Setup
-- ============================================================

CREATE DATABASE IF NOT EXISTS hashguard_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hashguard_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)        NOT NULL,
    email      VARCHAR(180)        NOT NULL UNIQUE,
    password   VARCHAR(255)        NOT NULL,          -- bcrypt hash
    role       ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Seed: default admin account
-- Password: Admin@1234
-- (bcrypt hash generated with password_hash("Admin@1234", PASSWORD_BCRYPT))
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
(
    'Site Administrator',
    'admin@hashguard.com',
    '$2y$12$4bvpXv0dtwPaeSkLz2NJmuDkd6Mn4IGXR6XFse6WhKyWowR5ZFPyq',
    'admin'
);

-- Note: The hash above may vary by environment.
-- After import, use the "Forgot / Reset" note in setup guide,
-- or simply register a new admin via the register page and
-- manually UPDATE the role to 'admin' in phpMyAdmin.
