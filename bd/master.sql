CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `auth_remember_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `selector` CHAR(24) NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remember_selector_unique` (`selector`),
  KEY `remember_user_index` (`user_id`),
  CONSTRAINT `remember_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `company_profile` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `address` VARCHAR(190) NOT NULL,
  `number` VARCHAR(20) NOT NULL,
  `district` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` CHAR(2) NOT NULL,
  `public_url` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `company_whatsapps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_whatsapps_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `message_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `content` TEXT NOT NULL,
  `send_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `landing_lead_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_model` VARCHAR(50) NOT NULL DEFAULT 'model-1',
  `color_palette` VARCHAR(50) NOT NULL DEFAULT 'palette-aurora',
  `bg_animation` VARCHAR(50) NOT NULL DEFAULT 'bg-particles',
  `btn_animation` VARCHAR(50) NOT NULL DEFAULT 'btn-pulse',
  `seo_title` VARCHAR(255) NULL DEFAULT 'Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos',
  `seo_description` TEXT NULL,
  `seo_image` VARCHAR(255) NULL,
  `headline` VARCHAR(255) NOT NULL DEFAULT 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
  `subheadline` TEXT NULL,
  `badge_text` VARCHAR(100) NOT NULL DEFAULT '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
  `button_text` VARCHAR(100) NOT NULL DEFAULT 'QUERO MEU ACESSO VIP AGORA',
  `button_subtext` VARCHAR(150) NULL DEFAULT '🔒 Acesso 100% gratuito e sem spam',
  `whatsapp_group_link` VARCHAR(255) NOT NULL DEFAULT 'https://chat.whatsapp.com/',
  `card1_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-discount-check',
  `card1_title` VARCHAR(100) NOT NULL DEFAULT 'Até 50% de Desconto Real',
  `card1_desc` VARCHAR(255) NOT NULL DEFAULT 'Preços exclusivos de atacado e varejo direto para membros do grupo.',
  `card2_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-flame',
  `card2_title` VARCHAR(100) NOT NULL DEFAULT 'Ofertas Relâmpago e Primeira Mão',
  `card2_desc` VARCHAR(255) NOT NULL DEFAULT 'Novidades e lançamentos liberados no grupo antes de todo mundo.',
  `card3_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-shield-lock',
  `card3_title` VARCHAR(100) NOT NULL DEFAULT '100% Original e com Garantia',
  `card3_desc` VARCHAR(255) NOT NULL DEFAULT 'Importados com nota fiscal, procedência garantida e suporte humanizado.',
  `modal_title` VARCHAR(150) NOT NULL DEFAULT '🎉 Parabéns! Seu Acesso VIP Está Liberado',
  `modal_desc` TEXT NULL,
  `modal_button_text` VARCHAR(100) NOT NULL DEFAULT 'ENTRAR NO GRUPO VIP DO WHATSAPP',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `leads_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `landing_lead_settings` (
  `id`, `template_model`, `color_palette`, `bg_animation`, `btn_animation`, `seo_title`, `seo_description`, `headline`, `subheadline`, `badge_text`, `button_text`, `button_subtext`,
  `whatsapp_group_link`, `card1_icon`, `card1_title`, `card1_desc`,
  `card2_icon`, `card2_title`, `card2_desc`, `card3_icon`, `card3_title`, `card3_desc`,
  `modal_title`, `modal_desc`, `modal_button_text`, `created_at`, `updated_at`
) VALUES (
  1,
  'model-1',
  'palette-aurora',
  'bg-particles',
  'btn-pulse',
  'Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos',
  'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
  'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
  'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
  '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
  'QUERO MEU ACESSO VIP AGORA',
  '🔒 Acesso 100% gratuito e sem spam',
  'https://chat.whatsapp.com/',
  'ti-discount-check',
  'Até 50% de Desconto Real',
  'Preços exclusivos de atacado e varejo direto para membros do grupo.',
  'ti-flame',
  'Ofertas Relâmpago e Primeira Mão',
  'Novidades e lançamentos liberados no grupo antes de todo mundo.',
  'ti-shield-lock',
  '100% Original e com Garantia',
  'Importados com nota fiscal, procedência garantida e suporte humanizado.',
  '🎉 Parabéns! Seu Acesso VIP Está Liberado',
  'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.',
  'ENTRAR NO GRUPO VIP DO WHATSAPP',
  NOW(),
  NOW()
);
