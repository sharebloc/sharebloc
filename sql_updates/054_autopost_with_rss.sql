ALTER TABLE posted_link ADD COLUMN `f_auto` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE user ADD COLUMN `f_autopost` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE user ADD COLUMN `autopost_tag_id` int(11) NOT NULL DEFAULT '0';

ALTER TABLE vendor ADD COLUMN `f_autopost` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE vendor ADD COLUMN `autopost_tag_id` int(11) NOT NULL DEFAULT '0';