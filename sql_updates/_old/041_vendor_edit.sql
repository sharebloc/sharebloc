ALTER TABLE vendor CHANGE COLUMN `overview` `description` text NOT NULL default '';

ALTER TABLE vendor ADD COLUMN `about` text NOT NULL default '' AFTER location;

-- ALTER TABLE vendor ADD COLUMN `facebook` varchar(200) NOT NULL default '' AFTER linkedin;
ALTER TABLE vendor ADD COLUMN `google_plus` varchar(200) NOT NULL default '' AFTER facebook;


-- 64 symbols can be too few to store data
ALTER TABLE vendor MODIFY COLUMN `vendor_name` varchar(200) NOT NULL default '';

ALTER TABLE user MODIFY COLUMN `twitter` varchar(200) NOT NULL default '';
ALTER TABLE user MODIFY COLUMN `facebook` varchar(200) NOT NULL default '';
ALTER TABLE user MODIFY COLUMN `linkedin` varchar(200) NOT NULL default '';
ALTER TABLE user MODIFY COLUMN `google_plus` varchar(200) NOT NULL default '';
ALTER TABLE user MODIFY COLUMN `website` varchar(200) NOT NULL default '';
ALTER TABLE user MODIFY COLUMN `rss` varchar(200) NOT NULL default '';