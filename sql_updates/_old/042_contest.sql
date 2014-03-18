ALTER TABLE posted_link ADD COLUMN `author_name` varchar(200) NOT NULL default '' AFTER text;
ALTER TABLE posted_link ADD COLUMN `f_contest` TINYINT(1) NOT NULL default 0;

ALTER TABLE user ADD COLUMN `f_contest_voter` TINYINT(1) NOT NULL default 0;
ALTER TABLE user CHANGE COLUMN `email_key` `unsubscribe_key` char(40) NOT NULL default '';
ALTER TABLE user ADD COLUMN `confirm_email_key` char(40) NOT NULL default '';
ALTER TABLE user CHANGE COLUMN `invites_count` `votes_count` tinyint(4) NOT NULL default '3';
UPDATE user SET votes_count=3;

UPDATE user SET confirm_email_key = CONCAT(MD5(RAND()), user_id);

ALTER TABLE user ADD UNIQUE KEY `unsubscribe_key_idx` (`unsubscribe_key`);
ALTER TABLE user ADD UNIQUE KEY `confirm_email_key_idx` (`confirm_email_key`);


CREATE TABLE `vote_contest` (
  `vote_id` int(11) NOT NULL auto_increment,
  `post_id` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  `value` tinyint(3) NOT NULL default '0',
  `date_added` datetime NOT NULL,
  `day_added` datetime NOT NULL,
  PRIMARY KEY  (`vote_id`),
  UNIQUE KEY `entity_id` (`post_id`,`user_id`,`day_added`),
  KEY `date_added_idx` (`day_added`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vote_contest_fk` FOREIGN KEY (`post_id`) REFERENCES `posted_link` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vote_contest_fk1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- preparing for contest tags
ALTER TABLE tag CHANGE COLUMN `tag_type`  `tag_type` enum('vendor','vendor_platform','vendor_cost','industry', 'contest') NOT NULL default 'vendor';
UPDATE tag SET tag_type='industry' WHERE tag_id=280 OR parent_tag_id=280;


INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        VALUES ('Contest category', 'contest', 0, NOW(), 'contest');

INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Advertising', 'contest', tag_id, NOW(), 'advertising'
        FROM tag WHERE code_name='contest';
INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Content Marketing', 'contest', tag_id, NOW(), 'content_marketing'
        FROM tag WHERE code_name='contest';
INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Demand Generation', 'contest', tag_id, NOW(), 'demand_generation'
        FROM tag WHERE code_name='contest';
INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Social Media', 'contest', tag_id, NOW(), 'social_media'
        FROM tag WHERE code_name='contest';
INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Marketing Automation', 'contest', tag_id, NOW(), 'marketing_automation'
        FROM tag WHERE code_name='contest';
INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Other', 'contest', tag_id, NOW(), 'other'
        FROM tag WHERE code_name='other';

UPDATE user SET votes_count=10 WHERE code_name='alen_mayer';
UPDATE user SET votes_count=10 WHERE code_name='ardath_albee';
UPDATE user SET votes_count=10 WHERE code_name='craig_rosenberg';
UPDATE user SET votes_count=10 WHERE code_name='dave_brock';
UPDATE user SET votes_count=10 WHERE code_name='douglas_karr';
UPDATE user SET votes_count=10 WHERE code_name='justin_gray';
UPDATE user SET votes_count=10 WHERE code_name='lori_richardson';
UPDATE user SET votes_count=10 WHERE code_name='matt_heinz';
UPDATE user SET votes_count=10 WHERE code_name='michael_brenner'; -- is not exist yet
UPDATE user SET votes_count=10 WHERE code_name='tibor_shanto';
UPDATE user SET votes_count=10 WHERE code_name='trish_bertuzzi';

UPDATE user SET votes_count=50 WHERE code_name='david_cheng';
UPDATE user SET votes_count=50 WHERE code_name='andrew_koller';

ALTER TABLE tag_selection CHANGE COLUMN `tag_type`  `tag_type` enum('vendor','vendor_platform','vendor_cost','industry', 'contest') NOT NULL default 'vendor';


INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Sales Enablement', 'contest', tag_id, NOW(), 'sales_enablement'
        FROM tag WHERE code_name='contest';

DELETE from tag where code_name='other' AND tag_type='contest';

INSERT INTO tag (tag_name, tag_type, parent_tag_id, date_added, code_name)
        SELECT 'Other', 'contest', tag_id, NOW(), 'other'
        FROM tag WHERE code_name='contest';