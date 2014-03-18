CREATE TABLE `invite_custom` (
  `inv_id` int(11) NOT NULL auto_increment,
  `confirm_key` char(128) NOT NULL,
  `comment` text,
  `created_ts` datetime NOT NULL,
  `deleted_ts` datetime default NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`inv_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `invite_custom_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `invite_custom_users` (
  `inv_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_ts` datetime NOT NULL,
  UNIQUE KEY `inv_id` (`inv_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `invite_custom_users_fk` FOREIGN KEY (`inv_id`) REFERENCES `invite_custom` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invite_custom_users_fk1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;