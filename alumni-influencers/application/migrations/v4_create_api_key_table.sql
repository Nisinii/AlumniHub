-- ============================================================
-- V4: API Key Management Tables
-- Bearer token auth for external clients (AR headset, developers).
-- ============================================================

USE `ar_alumni`;

-- api_keys
-- Stores bearer tokens for developer/client API access.
-- scope controls what endpoints the key can access:
--   read       → GET only (for AR headset reading featured alumni)
--   read_write → GET + POST (for developers building on the API)
--   admin      → full access including system endpoints
-- Keys are never deleted — soft revoked via is_active = 0.
CREATE TABLE `api_keys` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT(11) UNSIGNED NOT NULL,       -- developer who owns this key
  `key_name`      VARCHAR(100) NOT NULL,            -- friendly label e.g. "AR Headset Client"
  `api_key`       VARCHAR(64)  NOT NULL,            -- cryptographically random hex token
  `scope`         ENUM('read','read_write','admin') DEFAULT 'read',
  `is_active`     TINYINT(1)   DEFAULT 1,           -- 0 = revoked, stops working immediately
  `last_used_at`  DATETIME     DEFAULT NULL,
  `created_at`    DATETIME     NOT NULL,
  `revoked_at`    DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_api_key` (`api_key`),
  KEY `idx_api_keys_user` (`user_id`),
  CONSTRAINT `fk_api_keys_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- api_usage_logs
-- Records every API call made with a key.
-- Used for usage statistics and security auditing.
CREATE TABLE `api_usage_logs` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_key_id`     INT(11) UNSIGNED NOT NULL,
  `endpoint`       VARCHAR(255) NOT NULL,           -- e.g. /bidding/api/today
  `method`         VARCHAR(10)  NOT NULL,            -- GET, POST, DELETE
  `ip_address`     VARCHAR(45)  DEFAULT NULL,        -- caller IP
  `response_code`  SMALLINT     DEFAULT NULL,        -- HTTP status returned
  `called_at`      DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usage_key` (`api_key_id`),
  KEY `idx_usage_called_at` (`called_at`),
  CONSTRAINT `fk_usage_key` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;