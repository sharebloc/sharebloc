ALTER TABLE user ADD COLUMN linkedin_id varchar(64) NULL DEFAULT NULL AFTER linkedin;
ALTER TABLE user ADD UNIQUE KEY `linkedin_key` (`linkedin_id`);