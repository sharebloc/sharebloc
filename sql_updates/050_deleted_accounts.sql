CREATE TABLE `disabled_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `email` varchar(200) NOT NULL,
  `first_name` varchar(128) default NULL,
  `last_name` varchar(128) default NULL,
  `account_data` text,
  `created_ts` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;