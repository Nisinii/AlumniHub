-- ============================================================
-- V1: Users Table
-- Core authentication table.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `ar_alumni` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ar_alumni`;

-- users
-- Central auth table. Passwords stored as bcrypt hash only.
-- Display data (bio, image) lives in profiles table (3NF).
CREATE TABLE `users` (
  `id`                          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name`                  VARCHAR(100) NOT NULL,
  `last_name`                   VARCHAR(100) NOT NULL,
  `email`                       VARCHAR(255) NOT NULL,
  `password_hash`               VARCHAR(255) NOT NULL,         -- bcrypt cost 12, never plain text
  `role`                        ENUM('alumnus', 'developer', 'admin') DEFAULT 'alumnus',
  `verification_token`          VARCHAR(64)  DEFAULT NULL,     -- bin2hex(random_bytes(32))
  `verification_token_expiry`   DATETIME     DEFAULT NULL,     -- 24 hours from registration
  `is_verified`                 TINYINT(1)   DEFAULT 0,        -- 0 = blocked from login
  `reset_token`                 VARCHAR(64)  DEFAULT NULL,     -- single-use password reset token
  `reset_token_expiry`          DATETIME     DEFAULT NULL,     -- 1 hour from request
  `is_active`                   TINYINT(1)   DEFAULT 1,        -- admin can disable accounts
  `created_at`                  DATETIME     NOT NULL,
  `updated_at`                  DATETIME     DEFAULT NULL,
  `last_login_at`               DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_verification_token` (`verification_token`),         -- fast token lookup
  KEY `idx_reset_token` (`reset_token`)                        -- fast token lookup
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;