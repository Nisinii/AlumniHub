-- ============================================================
-- V3: Bidding System Tables
-- Blind auction: bids close 6PM, winner selected at midnight.
-- ============================================================

-- sponsor_offers
-- Sponsors offer money to alumni to promote their content.
-- Accepted offers become the alumni's bidding balance.
CREATE TABLE `sponsor_offers` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      INT(11) UNSIGNED NOT NULL,
  `sponsor_name` VARCHAR(255) NOT NULL,
  `amount`       DECIMAL(10,2) NOT NULL,
  `spent_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `description`  TEXT         DEFAULT NULL,
  `status`       ENUM('pending','accepted','declined') DEFAULT 'pending',
  `created_at`   DATETIME     NOT NULL,
  `updated_at`   DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sponsor_user` (`user_id`),
  CONSTRAINT `fk_sponsor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- bids
-- One active bid per user per day (UNIQUE key enforces this).
-- Blind: highest bid amount never exposed to clients.
-- status flow: active → won | lost | cancelled
CREATE TABLE `bids` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11) UNSIGNED NOT NULL,
  `bid_amount`  DECIMAL(10,2)    NOT NULL,
  `bid_date`    DATE             NOT NULL,        -- the slot being bid for
  `status`      ENUM('active','won','lost','cancelled') DEFAULT 'active',
  `is_winner`   TINYINT(1)       DEFAULT 0,       -- set to 1 at midnight selection
  `notified`    TINYINT(1)       DEFAULT 0,       -- 1 after win/lose email sent
  `created_at`  DATETIME         NOT NULL,
  `updated_at`  DATETIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_bid_date` (`user_id`, `bid_date`),   -- one bid per user per day
  KEY `idx_bids_date` (`bid_date`),
  KEY `idx_bids_status` (`status`),
  CONSTRAINT `fk_bids_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- featured_alumni
-- One record per day. Created at midnight when winner is activated.
-- This is the table the AR client reads via GET /bidding/api/today.
CREATE TABLE `featured_alumni` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      INT(11) UNSIGNED NOT NULL,
  `bid_id`       INT(11) UNSIGNED NOT NULL,
  `feature_date` DATE             NOT NULL,
  `activated_at` DATETIME         DEFAULT NULL,   -- set at midnight activation
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_feature_date` (`feature_date`),  -- one featured alumni per day
  KEY `idx_featured_user` (`user_id`),
  CONSTRAINT `fk_featured_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_featured_bid`  FOREIGN KEY (`bid_id`)  REFERENCES `bids` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- alumni_events
-- Attending one university event per month grants a 4th bid slot.
-- Base monthly limit is 3 wins. +1 with event attendance = 4.
CREATE TABLE `alumni_events` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11) UNSIGNED NOT NULL,
  `event_name`  VARCHAR(255)     NOT NULL,
  `event_date`  DATE             NOT NULL,
  `created_at`  DATETIME         NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_events_user` (`user_id`),
  CONSTRAINT `fk_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;