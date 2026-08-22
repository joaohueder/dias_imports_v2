CREATE TABLE `landing_lead_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `headline` VARCHAR(255) NOT NULL DEFAULT 'Acesso Exclusivo: Ofertas e Descontos VIP',
  `subheadline` TEXT NULL,
  `badge_text` VARCHAR(100) NOT NULL DEFAULT 'GRUPO VIP DIAS IMPORTS • VAGAS LIMITADAS',
  `button_text` VARCHAR(100) NOT NULL DEFAULT 'QUERO ENTRAR NO GRUPO VIP',
  `button_subtext` VARCHAR(150) NULL DEFAULT '⚡ Acesso 100% gratuito e seguro',
  `whatsapp_group_link` VARCHAR(255) NOT NULL DEFAULT 'https://chat.whatsapp.com/',
  `card1_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-percentage',
  `card1_title` VARCHAR(100) NOT NULL DEFAULT 'Descontos de até 50%',
  `card1_desc` VARCHAR(255) NOT NULL DEFAULT 'Ofertas antecipadas direto no seu WhatsApp antes de ir para o site.',
  `card2_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-bolt',
  `card2_title` VARCHAR(100) NOT NULL DEFAULT 'Lotes Exclusivos',
  `card2_desc` VARCHAR(255) NOT NULL DEFAULT 'Acesso prioritário a produtos importados originais e lançamentos.',
  `card3_icon` VARCHAR(50) NOT NULL DEFAULT 'ti-shield-check',
  `card3_title` VARCHAR(100) NOT NULL DEFAULT 'Garantia e Procedência',
  `card3_desc` VARCHAR(255) NOT NULL DEFAULT 'Produtos 100% autênticos com suporte dedicado e nota fiscal.',
  `modal_title` VARCHAR(150) NOT NULL DEFAULT '🎉 Cadastro Confirmado com Sucesso!',
  `modal_desc` TEXT NULL,
  `modal_button_text` VARCHAR(100) NOT NULL DEFAULT 'ENTRAR NO GRUPO VIP AGORA',
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

INSERT INTO `landing_lead_settings` (
  `id`, `headline`, `subheadline`, `badge_text`, `button_text`, `button_subtext`,
  `whatsapp_group_link`, `card1_icon`, `card1_title`, `card1_desc`,
  `card2_icon`, `card2_title`, `card2_desc`, `card3_icon`, `card3_title`, `card3_desc`,
  `modal_title`, `modal_desc`, `modal_button_text`, `created_at`, `updated_at`
) VALUES (
  1,
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
