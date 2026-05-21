-- SQL Script to create order_settings table
-- Run this in phpMyAdmin or MySQL command line

CREATE TABLE IF NOT EXISTS `order_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_status` TINYINT(1) NOT NULL DEFAULT 1,
  `order_off_message` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default record
INSERT INTO `order_settings` (`order_status`, `order_off_message`, `created_at`, `updated_at`) 
VALUES (1, 'Order creation is currently disabled. Please try again later.', NOW(), NOW());
