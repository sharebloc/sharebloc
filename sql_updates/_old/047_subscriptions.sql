CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(200) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `confirm_key` char(32) default NULL,
  `user_id` int(11) default NULL,
  `created_ts` datetime NOT NULL,
  `confirmed_ts` datetime default NULL,
  `deleted_ts` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE user ADD COLUMN `join_source` enum('none','contest','subscription') NOT NULL default 'none';
UPDATE user SET join_source = 'contest' WHERE f_contest_voter=1;

UPDATE user SET cookie_key = MD5(RAND()) WHERE cookie_key='';
ALTER TABLE user ADD UNIQUE KEY `cookie_key_idx` (`cookie_key`);

insert into subscriptions
(email, tag_id, confirm_key, created_ts, confirmed_ts)
(SELECT email, 1, MD5(RAND()), now(), now() from user
		where date_confirmed!='0000-00-00 00:00:00'
        and f_contest_voter=1
        and notify_contest=1
        and notify_weekly=1
        );