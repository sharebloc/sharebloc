ALTER TABLE tag_selection ADD COLUMN f_explicit tinyint(4) NOT NULL DEFAULT 1 AFTER tag_type;

ALTER TABLE tag_selection modify COLUMN `entity_type` enum('vendor','company','user','posted_link','question','review') NOT NULL DEFAULT 'vendor';