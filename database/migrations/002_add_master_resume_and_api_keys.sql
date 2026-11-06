-- Migration: Add master_resume and api_keys columns to settings table
-- Date: 2026-07-23
-- Purpose: Migrate localStorage data (masterResume, apiKey_*) to MySQL
-- MySQL 5.7 (the production server) does not support ADD COLUMN IF NOT EXISTS.
-- These guarded statements are safe to run on a fresh or already-migrated DB.
SET @schema_name = DATABASE();

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE settings ADD COLUMN master_resume LONGTEXT NULL',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @schema_name
      AND TABLE_NAME = 'settings'
      AND COLUMN_NAME = 'master_resume'
);
PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE settings ADD COLUMN api_keys JSON NULL',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @schema_name
      AND TABLE_NAME = 'settings'
      AND COLUMN_NAME = 'api_keys'
);
PREPARE statement FROM @sql;
EXECUTE statement;
DEALLOCATE PREPARE statement;
