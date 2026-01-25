-- Limpiar tabla de migraciones fallidas
DELETE FROM `_prisma_migrations` WHERE migration_name LIKE '20260124_%' AND migration_name != '20260124034449_add_config_role_user' AND migration_name != '20260124035813_add_credit_payment';
