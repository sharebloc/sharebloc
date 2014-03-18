CREATE TABLE `notifications` (
  `n_id` int(11) NOT NULL auto_increment,
  `post_type` enum('question','review','posted_link','comment') NOT NULL default 'posted_link',
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_sent` tinyint(4) NOT NULL default '0',
  `created_ts` datetime NOT NULL,
  `comment_id` int(11) default NULL,
  PRIMARY KEY  (`n_id`),
  UNIQUE KEY `post_type_id_user` (`post_type`,`post_id`,`user_id`,`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

ALTER TABLE user ADD COLUMN `notify_by_email` tinyint(4) NOT NULL DEFAULT 1;