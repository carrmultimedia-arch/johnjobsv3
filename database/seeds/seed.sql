-- Seed data for testing
INSERT INTO users (name, email, role) VALUES ('Test User', 'test@example.com', 'user') ON DUPLICATE KEY UPDATE name = name;
INSERT INTO companies (name, industry, location, status) VALUES ('Example Corp', 'Technology', 'Austin, TX', 'active') ON DUPLICATE KEY UPDATE name = name;
INSERT INTO settings (`key`, value, category) VALUES ('user_name', 'Job Seeker', 'profile') ON DUPLICATE KEY UPDATE `key` = `key`;
