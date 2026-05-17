-- ============================================================
-- Description: Secure storage for the API keys the dashboard uses.
-- ============================================================

USE `client_dashboard`;

CREATE TABLE `api_credentials` (
  `id`                  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_user_id`       INT(11) UNSIGNED NOT NULL,     -- Who added/owns this key
  `key_name`            VARCHAR(50) NOT NULL,          -- e.g., 'Main CW1 Analytics Key'
  `api_key_value`       VARCHAR(255) NOT NULL,         -- The actual Bearer token
  `permissions`         JSON NOT NULL,                 -- e.g., ["read:alumni", "read:analytics"]
  `is_active`           TINYINT(1) DEFAULT 1,
  `created_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_api_staff` FOREIGN KEY (`staff_user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;