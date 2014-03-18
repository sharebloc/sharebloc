CREATE TABLE `invite_front` (
  `inv_id` int(11) NOT NULL auto_increment,
  `confirm_key` char(32) NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `text` text,
  `created_ts` datetime NOT NULL,
  `deleted_ts` datetime default NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('new','deleted','confirmed') NOT NULL default 'new',
  `invited_user_id` int(11) default NULL,
  PRIMARY KEY  (`inv_id`),
  UNIQUE KEY `req_id` (`inv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

ALTER TABLE user ADD COLUMN `new_vs_allowed` tinyint(4) NOT NULL DEFAULT 0;
ALTER TABLE user ADD COLUMN `invites_count` tinyint(4) NOT NULL DEFAULT 5;

-- UPDATE user SET new_vs_allowed=1 WHERE code_name in ('');