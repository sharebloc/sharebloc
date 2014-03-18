CREATE TABLE `oauth` (
  `oauth_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `provider` varchar(32) NOT NULL,
  `provider_uid` varchar(64) NOT NULL,
  `hauth_info` text,
  `provider_info` text NOT NULL,
  `created_ts` datetime default NULL,
  PRIMARY KEY  (`oauth_id`),
  UNIQUE KEY `provider` (`provider`,`provider_uid`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

ALTER TABLE oauth ADD COLUMN user_contacts text NOT NULL default '' AFTER hauth_info;
ALTER TABLE oauth CHANGE COLUMN user_contacts `user_contacts` mediumtext NOT NULL default '';


INSERT INTO oauth
                        (user_id, provider, provider_uid,
                        hauth_info, provider_info, user_contacts, created_ts)

                        SELECT user_id, 'LinkedIn', linkedin_id, '[]', '[]', '[]', now() FROM user
                        where linkedin_id is not null and linkedin_id != ''

-- execute after successfully execution of prev queries
ALTER TABLE user DROP COLUMN linkedin_id;