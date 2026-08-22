-- ==============================================================================
-- MIGRAÇÃO SEQUENCIAL 012: ADICIONAR ROLE E PERMISSIONS NA TABELA USERS
-- ==============================================================================

ALTER TABLE `users`
ADD COLUMN `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user' AFTER `password_hash`,
ADD COLUMN `permissions` LONGTEXT NULL AFTER `role`;

-- Atualizar o primeiro usuário (admin padrão) para admin
UPDATE `users` SET `role` = 'admin' WHERE `id` = 1;
