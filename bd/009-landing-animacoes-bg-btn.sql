-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 009: ADICIONAR ANIMAÇÕES DE BACKGROUND E BOTÃO CTA
-- ==============================================================================

ALTER TABLE `landing_lead_settings` 
ADD COLUMN `bg_animation` VARCHAR(50) NOT NULL DEFAULT 'bg-particles' AFTER `color_palette`,
ADD COLUMN `btn_animation` VARCHAR(50) NOT NULL DEFAULT 'btn-pulse' AFTER `bg_animation`;
