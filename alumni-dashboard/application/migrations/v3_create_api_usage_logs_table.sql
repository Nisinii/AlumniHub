-- ============================================================
-- Description: Tracks usage statistics and endpoints accessed.
-- ============================================================

USE `client_dashboard`;

CREATE TABLE `api_usage_logs` (
  `id`                  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `credential_id`       INT(11) UNSIGNED NOT NULL,     -- Which key was used
  `endpoint_accessed`   VARCHAR(255) NOT NULL,         -- e.g., '/api/v1/analytics/skills-gap'
  `response_status`     INT(3) NOT NULL,               -- e.g., 200, 403, 500
  `accessed_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_accessed_at` (`accessed_at`),
  CONSTRAINT `fk_log_credential` FOREIGN KEY (`credential_id`) REFERENCES `api_credentials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;