ALTER TABLE user ADD COLUMN google_plus varchar(64) NULL DEFAULT NULL AFTER linkedin;
ALTER TABLE user ADD COLUMN website varchar(64) NULL DEFAULT NULL AFTER google_plus;