ALTER TABLE user ADD COLUMN votes_count tinyint(4) NOT NULL default '3';

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

UPDATE user SET votes_count=10 WHERE code_name='david_cheng';
UPDATE user SET votes_count=10 WHERE code_name='andrew_koller';

ALTER TABLE posted_link CHANGE COLUMN `f_contest` `f_contest` tinyint(4) NOT NULL default '0';

ALTER TABLE user ADD COLUMN f_get_sponsor_email tinyint(1) NOT NULL default '0';
--SELECT user_id FROM user WHERE f_get_sponsor_email=1;