CREATE TABLE `meta_ads_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pixel_id` VARCHAR(50) NOT NULL DEFAULT '',
  `access_token_encrypted` TEXT NOT NULL,
  `test_event_code` VARCHAR(50) NULL,
  `last_test_status` VARCHAR(20) NULL,
  `last_tested_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
