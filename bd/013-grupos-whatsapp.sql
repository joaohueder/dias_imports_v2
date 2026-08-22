-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 013: TABELA DE GRUPOS DE WHATSAPP
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `whatsapp_groups` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `group_jid` VARCHAR(191) NOT NULL,
  `instance_name` VARCHAR(191) NOT NULL DEFAULT '',
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `participants_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `avatar_url` TEXT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `is_admin_only` TINYINT(1) NOT NULL DEFAULT 0,
  `is_restricted` TINYINT(1) NOT NULL DEFAULT 0,
  `category` VARCHAR(100) NULL,
  `last_synced_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_instance_group` (`instance_name`, `group_jid`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
