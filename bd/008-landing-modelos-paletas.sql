ALTER TABLE `landing_lead_settings`
  ADD COLUMN `template_model` VARCHAR(50) NOT NULL DEFAULT 'model-1' AFTER `id`,
  ADD COLUMN `color_palette` VARCHAR(50) NOT NULL DEFAULT 'palette-aurora' AFTER `template_model`;
