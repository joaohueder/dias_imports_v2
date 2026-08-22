-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 010: ADICIONAR CAMPOS DE SEO E COMPARTILHAMENTO
-- ==============================================================================

ALTER TABLE `landing_lead_settings` 
ADD COLUMN `seo_title` VARCHAR(255) NOT NULL DEFAULT 'Grupo VIP Dias Imports' AFTER `btn_animation`,
ADD COLUMN `seo_description` TEXT NULL AFTER `seo_title`;