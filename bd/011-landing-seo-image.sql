-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 011: ADICIONAR CAMPO DE IMAGEM PERSONALIZADA DE SEO (OG IMAGE)
-- ==============================================================================

ALTER TABLE `landing_lead_settings` 
ADD COLUMN `seo_image` VARCHAR(255) NULL AFTER `seo_description`;
