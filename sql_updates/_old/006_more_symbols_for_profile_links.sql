-- done on all instances

ALTER TABLE vendor CHANGE COLUMN `twitter` `twitter` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE vendor CHANGE COLUMN `facebook` `facebook` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE vendor CHANGE COLUMN `linkedin` `linkedin` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE vendor CHANGE COLUMN `rss` `rss` varchar(200) NOT NULL DEFAULT '';

ALTER TABLE company CHANGE COLUMN `twitter` `twitter` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE company CHANGE COLUMN `facebook` `facebook` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE company CHANGE COLUMN `linkedin` `linkedin` varchar(200) NOT NULL DEFAULT '';
ALTER TABLE company CHANGE COLUMN `rss` `rss` varchar(200) NOT NULL DEFAULT '';