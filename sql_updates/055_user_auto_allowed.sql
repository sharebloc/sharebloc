ALTER TABLE user ADD COLUMN f_auto_allowed tinyint(1) NOT NULL DEFAULT 0 AFTER autopost_tag_id;

UPDATE user SET f_auto_allowed = 1 WHERE user_id IN (2, 951, 3851, 2200, 1702, 1504, 1473, 1582, 1419, 1383, 1236, 2295);