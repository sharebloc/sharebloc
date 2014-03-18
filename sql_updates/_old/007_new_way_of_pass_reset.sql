ALTER TABLE user DROP COLUMN `reset_passw_key`;
ALTER TABLE user DROP COLUMN `reset_passw_ts`;

ALTER TABLE user ADD COLUMN `reset_passw_key` CHAR(32) NOT NULL DEFAULT '';
ALTER TABLE user ADD COLUMN `reset_passw_ts` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';