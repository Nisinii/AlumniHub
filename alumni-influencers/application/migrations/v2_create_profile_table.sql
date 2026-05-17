-- ============================================================
-- V2: Profile Tables
-- Alumni display data, separated from auth (3NF compliance).
-- ============================================================

USE `ar_alumni`;

-- profiles (1-to-1 with users)
-- UNIQUE on user_id enforces the 1-to-1 relationship.
-- Separated from users so auth data and display data are independent.
CREATE TABLE `profiles` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT(11) UNSIGNED NOT NULL,
  `bio`           TEXT             DEFAULT NULL,
  `linkedin_url`  VARCHAR(500)     DEFAULT NULL,
  `profile_image` VARCHAR(255)     DEFAULT NULL,  -- filename only, full URL built in model
  `created_at`    DATETIME         NOT NULL,
  `updated_at`    DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- degrees (1-to-many with users)
CREATE TABLE `degrees` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11) UNSIGNED NOT NULL,
  `degree_name`     VARCHAR(255) NOT NULL,
  `institution`     VARCHAR(255) NOT NULL,
  `degree_url`      VARCHAR(500) DEFAULT NULL,    -- URL to official university degree page
  `completion_date` DATE         DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL,
  `updated_at`      DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_degrees_user` (`user_id`),
  CONSTRAINT `fk_degrees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- certifications
CREATE TABLE `certifications` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11) UNSIGNED NOT NULL,
  `cert_name`       VARCHAR(255) NOT NULL,
  `issuing_body`    VARCHAR(255) NOT NULL,
  `cert_url`        VARCHAR(500) DEFAULT NULL,    -- URL to certification course page
  `completion_date` DATE         DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL,
  `updated_at`      DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_certifications_user` (`user_id`),
  CONSTRAINT `fk_certifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- licences
CREATE TABLE `licences` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11) UNSIGNED NOT NULL,
  `licence_name`    VARCHAR(255) NOT NULL,
  `awarding_body`   VARCHAR(255) NOT NULL,
  `licence_url`     VARCHAR(500) DEFAULT NULL,    -- URL to licence awarding body
  `completion_date` DATE         DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL,
  `updated_at`      DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_licences_user` (`user_id`),
  CONSTRAINT `fk_licences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- courses (short professional courses)
CREATE TABLE `courses` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(11) UNSIGNED NOT NULL,
  `course_name`     VARCHAR(255) NOT NULL,
  `provider`        VARCHAR(255) NOT NULL,
  `course_url`      VARCHAR(500) DEFAULT NULL,    -- URL to course page
  `completion_date` DATE         DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL,
  `updated_at`      DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_courses_user` (`user_id`),
  CONSTRAINT `fk_courses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- employment (end_date NULL = currently employed)
CREATE TABLE `employment` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11) UNSIGNED NOT NULL,
  `job_title`   VARCHAR(255) NOT NULL,
  `company`     VARCHAR(255) NOT NULL,
  `start_date`  DATE         NOT NULL,
  `end_date`    DATE         DEFAULT NULL,        -- NULL means currently employed here
  `description` TEXT         DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL,
  `updated_at`  DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_employment_user` (`user_id`),
  CONSTRAINT `fk_employment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;