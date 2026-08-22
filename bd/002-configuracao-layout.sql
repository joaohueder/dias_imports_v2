CREATE TABLE `app_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `app_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`)
VALUES ('layout_max_width', '1200', NOW(), NOW());
