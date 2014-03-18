ALTER TABLE user ADD COLUMN `cookie_key` char(32) NOT NULL DEFAULT '';

-- was removed in previous commit
DROP TABLE wizard_status;