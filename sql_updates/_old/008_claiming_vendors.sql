-- done everywhere
CREATE TABLE `claim` (
  `claim_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('vendor','company','user','group') NOT NULL,
  `claim_key` char(32) NOT NULL,
  `created_ts` datetime NOT NULL,
  `deleted_ts` datetime default NULL,
  PRIMARY KEY  (`claim_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

ALTER TABLE vendor ADD COLUMN `owner_user_id` INT (11) NOT NULL DEFAULT 0;
ALTER TABLE vendor ADD COLUMN `f_claim_locked` tinyint(4) NOT NULL DEFAULT 0;