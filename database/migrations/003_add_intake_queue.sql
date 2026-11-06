-- Migration: Add intake_queue table for Job Acquisition Pipeline Phase 1
-- This table serves as the inbox stage where jobs land first, get reviewed/scored,
-- and only qualified ones get promoted into JobHunt's real tables.

CREATE TABLE IF NOT EXISTS intake_queue (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(255) NOT NULL,
  job_title VARCHAR(255) NOT NULL,
  location VARCHAR(255),
  source VARCHAR(100),           -- LinkedIn, Indeed, Upwork, etc.
  score DECIMAL(3,1),            -- e.g. 9.5
  summary TEXT,
  salary VARCHAR(100),
  employment_type VARCHAR(100),
  apply_link VARCHAR(500),
  required_skills TEXT,
  why_match TEXT,
  notes TEXT,
  status ENUM('new','imported','dismissed') NOT NULL DEFAULT 'new',
  date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_actioned DATETIME NULL,
  imported_application_id INT NULL,   -- FK to applications.id once imported
  INDEX idx_status (status),
  INDEX idx_date_added (date_added),
  INDEX idx_company_name (company_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
