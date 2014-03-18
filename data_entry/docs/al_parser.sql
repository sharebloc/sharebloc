
DROP TABLE IF EXISTS `data_al_vendors`;

CREATE TABLE `data_al_vendors` (
  `id` int(11) NOT NULL auto_increment,
  `al_id` int(11) NOT NULL,
  `hidden` smallint(6) NOT NULL,
  `name` varchar(100) default NULL,
  `raw_data` text,
  `created_at` varchar(100) default NULL,
  `date_added` timestamp NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5947 DEFAULT CHARSET=utf8;