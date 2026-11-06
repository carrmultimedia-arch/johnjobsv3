-- 1. Prospect Cue needs a workflow status; nothing in the schema tracks it.
ALTER TABLE prospects
  ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'new' AFTER rating;

-- 2. saved_searches has no soft-delete column; every other table does.
ALTER TABLE saved_searches
  ADD COLUMN deleted_at DATETIME NULL AFTER updated_at;

-- 3. settings is missing three profile fields the frontend's Settings tab needs.
ALTER TABLE settings
  ADD COLUMN target_location VARCHAR(255) NULL AFTER arrangement,
  ADD COLUMN bio TEXT NULL AFTER target_location,
  ADD COLUMN master_resume LONGTEXT NULL AFTER bio;

-- 4. Seed a single local user + a single settings row if not already present.
INSERT IGNORE INTO users (id, name, email, role)
  VALUES (1, 'John Carr', 'john@carrmultimedia.com', 'user');
INSERT IGNORE INTO settings (id, user_id, name, email, phone)
  VALUES (1, 1, 'John Carr', 'john@carrmultimedia.com', '(713) 899-1776');

-- 5. Two tables genuinely don't exist anywhere in the schema and the frontend
--    needs them (Network tab's target-company list, LinkedIn activity counter).
--    These are net-new, not redesigns of anything existing.
CREATE TABLE IF NOT EXISTS target_companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company VARCHAR(255) NOT NULL,
  target_role VARCHAR(255) NULL,
  hiring_manager VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS linkedin_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  week_of DATE NOT NULL,
  connections INT DEFAULT 0,
  messages INT DEFAULT 0,
  responses INT DEFAULT 0,
  calls INT DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
