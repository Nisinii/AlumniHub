-- ============================================================
-- Description: Allows staff to save custom dashboard views/reports.
-- ============================================================

USE `client_dashboard`;

CREATE TABLE `saved_filters` (
  `id`                  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_user_id`       INT(11) UNSIGNED NOT NULL,
  `preset_name`         VARCHAR(100) NOT NULL,         -- e.g., '2025 CS Cloud Skills Gap'
  `filter_config`       JSON NOT NULL,                 -- Stores selected graduation dates, programs, etc.
  `created_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_filter_staff` FOREIGN KEY (`staff_user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;