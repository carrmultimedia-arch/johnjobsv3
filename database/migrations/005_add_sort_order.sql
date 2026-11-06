-- Migration: Add sort_order column for custom drag-and-drop sorting
-- Adds sort_order to applications, prospects, and jobs tables

-- Add sort_order to applications table
ALTER TABLE applications ADD COLUMN sort_order INT NULL DEFAULT NULL AFTER deleted_at;
ALTER TABLE applications ADD INDEX idx_sort_order (sort_order);

-- Add sort_order to prospects table  
ALTER TABLE prospects ADD COLUMN sort_order INT NULL DEFAULT NULL AFTER deleted_at;
ALTER TABLE prospects ADD INDEX idx_sort_order (sort_order);

-- Add sort_order to jobs table
ALTER TABLE jobs ADD COLUMN sort_order INT NULL DEFAULT NULL AFTER deleted_at;
ALTER TABLE jobs ADD INDEX idx_sort_order (sort_order);
