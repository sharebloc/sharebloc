CREATE TABLE answer_back AS SELECT * FROM answer WHERE 1;

ALTER TABLE answer RENAME TO comment;

ALTER TABLE comment CHANGE COLUMN `question_id` `post_id` int(11) NOT NULL default '0';
ALTER TABLE comment CHANGE COLUMN `answer_id` `comment_id`  int(11) NOT NULL auto_increment;
ALTER TABLE comment CHANGE COLUMN `answer_text` `comment_text` text NOT NULL;
ALTER TABLE comment DROP COLUMN `field8`;

ALTER TABLE comment ADD COLUMN `post_type` enum('posted_link','question', 'review') NULL AFTER comment_id;
UPDATE comment set `post_type` = 'question';
ALTER TABLE comment MODIFY COLUMN `post_type` enum('posted_link','question', 'review') NOT NULL;

-- depended tables
ALTER TABLE relation MODIFY COLUMN `entity_type` enum('question','comment','posted_link', 'answer') NOT NULL default 'question';
UPDATE relation SET entity_type = 'comment' WHERE entity_type = 'answer';
ALTER TABLE relation MODIFY COLUMN `entity_type` enum('question','comment','posted_link') NOT NULL default 'question';

ALTER TABLE group_permission_answer RENAME TO group_permission_comment;
ALTER TABLE group_permission_comment CHANGE COLUMN `answer_id` `comment_id` int(11) NOT NULL default '0';

ALTER TABLE vote MODIFY COLUMN `entity_type` enum('question','answer','review','posted_link', 'comment') NOT NULL default 'question';
UPDATE vote SET entity_type = 'comment' WHERE entity_type = 'answer';
ALTER TABLE vote MODIFY COLUMN `entity_type` enum('question', 'review','posted_link', 'comment') NOT NULL default 'question';

ALTER TABLE stats MODIFY COLUMN entity_type enum('vendor','company','user','group','question','answer','posted_link', 'comment') NOT NULL default 'vendor';
UPDATE stats SET entity_type = 'comment' WHERE entity_type = 'answer';
ALTER TABLE stats MODIFY COLUMN entity_type enum('vendor','company','user','group','question','posted_link', 'comment') NOT NULL default 'vendor';
