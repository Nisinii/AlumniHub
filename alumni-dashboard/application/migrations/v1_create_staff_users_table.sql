-- ============================================================
-- Description: Creates the dedicated CW2 database.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `client_dashboard` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================================
-- Description: Core authentication table for University Staff/Admins.
-- ============================================================

USE `client_dashboard`;

CREATE TABLE `staff_users` (
  `id`                  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name`          VARCHAR(100) NOT NULL,
  `last_name`           VARCHAR(100) NOT NULL,
  `email`               VARCHAR(255) NOT NULL,
  `password_hash`       VARCHAR(255) NOT NULL,         -- bcrypt cost 10+, never plain text
  `department`          VARCHAR(100) DEFAULT NULL,     -- e.g., 'Computer Science', 'Careers'
  `verification_token`  VARCHAR(64)  DEFAULT NULL,     -- bin2hex(random_bytes(32))
  `verification_expiry` DATETIME     DEFAULT NULL,     -- 24 hours from registration
  `is_verified`         TINYINT(1)   DEFAULT 0,        -- 0 = blocked from login
  `reset_token`         VARCHAR(64)  DEFAULT NULL,     -- single-use password reset token
  `reset_expiry`        DATETIME     DEFAULT NULL,     -- 1 hour from request
  `created_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at`       DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_staff_email` (`email`),
  KEY `idx_staff_verification` (`verification_token`),
  KEY `idx_staff_reset` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;