-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 014: CENTRAL DE TRABALHO (JOBS & QUEUE)
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `system_jobs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `job_key` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `min_delay_seconds` INT UNSIGNED NOT NULL DEFAULT 5,
  `max_delay_seconds` INT UNSIGNED NOT NULL DEFAULT 20,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `system_job_queue` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `job_key` VARCHAR(100) NOT NULL,
  `item_reference` VARCHAR(191) NULL,
  `payload` LONGTEXT NULL,
  `status` ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
  `scheduled_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `started_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `error_message` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_status_scheduled` (`status`, `scheduled_at`),
  KEY `idx_job_key` (`job_key`),
  KEY `idx_item_reference` (`item_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insere o trabalho padrão para Atualizar Grupos do WhatsApp se não existir
INSERT INTO `system_jobs` (`job_key`, `name`, `description`, `is_active`, `min_delay_seconds`, `max_delay_seconds`)
VALUES (
  'sync_whatsapp_groups',
  'Atualizar Todos os Grupos do WhatsApp',
  'Busca informações detalhadas, participantes e foto de cada grupo do WhatsApp cadastrado no sistema via Evolution API.',
  1,
  5,
  20
) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
