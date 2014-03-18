CREATE TABLE `email_log` (
  `email_id` int(11) NOT NULL auto_increment,
  `email` char(128) NOT NULL,
  `sent_ts` datetime NOT NULL,
  `email_code` char(40) default NULL,
  `type` char(40) NOT NULL,
  `opens_count` int(11) NOT NULL default '0',
  `last_open_ts` datetime default NULL,
  PRIMARY KEY  (`email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;