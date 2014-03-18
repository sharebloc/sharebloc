CREATE TABLE `posted_link` (
  `post_id` int(11) NOT NULL auto_increment,
  `code_name` varchar(128) character set latin1 NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `title` text character set latin1 NOT NULL,
  `url` text character set latin1 NOT NULL,
  `text` text character set latin1 NOT NULL,
  `logo_id` int(11) default NULL,
  `privacy` enum('public','custom','anonymous') character set latin1 NOT NULL default 'public',
  `status` enum('active','inactive','review') character set latin1 NOT NULL default 'active',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 0 kB';

ALTER TABLE tag_selection modify COLUMN `entity_type` enum('vendor','company','user','posted_link') NOT NULL default 'vendor';

ALTER TABLE relation modify COLUMN `entity_type` enum('question','answer','posted_link') NOT NULL default 'question';

ALTER TABLE vote modify COLUMN `entity_type` enum('question','answer','review','posted_link') NOT NULL default 'question';

ALTER TABLE stats modify COLUMN `entity_type` enum('vendor','company','user','group','question','answer','posted_link') NOT NULL default 'vendor';