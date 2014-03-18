-- done everywhere

-- https://vendorstack.atlassian.net/browse/VEN-106
CREATE TABLE `invite` (
  `inv_id` int(11) NOT NULL auto_increment,
  `vendor_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `confirm_key` char(32) NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `created_ts` datetime NOT NULL,
  `email` varchar(128) NOT NULL,
  `deleted_ts` datetime default NULL,
  `user_id` int(11) NOT NULL,
  `new_company` tinyint(4) NOT NULL,
  `status` enum('new','deleted','confirmed') NOT NULL default 'new',
  `invited_user_id` int(11) default NULL,
  PRIMARY KEY  (`inv_id`),
  UNIQUE KEY `req_id` (`inv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;