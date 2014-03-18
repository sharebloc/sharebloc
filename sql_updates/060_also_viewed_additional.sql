ALTER TABLE also_viewed modify COLUMN entity_type enum('vendor','question','posted_link') NOT NULL default 'question';
DROP TABLE stats;