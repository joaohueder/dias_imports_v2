CREATE TABLE `evolution_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `base_url` VARCHAR(255) NOT NULL,
  `api_key_encrypted` TEXT NOT NULL,
  `min_delay_seconds` INT UNSIGNED NOT NULL DEFAULT 5,
  `max_delay_seconds` INT UNSIGNED NOT NULL DEFAULT 30,
  `default_instance_name` VARCHAR(80) NULL,
  `last_test_status` VARCHAR(20) NULL,
  `last_tested_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
