CREATE TABLE `sharebloc_join` (
  `id` int(11) NOT NULL auto_increment,
  `email` text NOT NULL,
  `created_ts` datetime NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `email` (`email`(1))
) ENGINE=MyISAM AUTO_INCREMENT=437 DEFAULT CHARSET=utf8;