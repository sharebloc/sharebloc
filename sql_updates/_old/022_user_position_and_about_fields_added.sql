-- NOT NULL default '' as old code does not support nulls
ALTER TABLE user ADD COLUMN position varchar(128) NOT NULL default '' AFTER location;
ALTER TABLE user ADD COLUMN about text NOT NULL default '' AFTER position;
