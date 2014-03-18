CREATE TABLE `repost` (
  `repost_id` int(11) NOT NULL auto_increment,
  `entity_id` int(11) NOT NULL default '0',
  `entity_type` enum('question','posted_link') NOT NULL default 'posted_link',
  `user_id` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`repost_id`),
  UNIQUE KEY `entity_id` (`entity_id`,`entity_type`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `repost_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;