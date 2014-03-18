ALTER TABLE user ADD COLUMN `notify_weekly` tinyint(4) NOT NULL DEFAULT 1 AFTER notify_by_email;
ALTER TABLE user ADD COLUMN `notify_post_responded` tinyint(4) NOT NULL DEFAULT 1 AFTER notify_weekly;
ALTER TABLE user ADD COLUMN `notify_comment_responded` tinyint(4) NOT NULL DEFAULT 1 AFTER notify_post_responded;
ALTER TABLE user ADD COLUMN `notify_product_update` tinyint(4) NOT NULL DEFAULT 1 AFTER notify_comment_responded;
/*
UPDATE user
SET notify_weekly=0,
    notify_post_responded=0,
    notify_comment_responded=0,
    notify_product_update=0
WHERE notify_by_email=0;
*/
ALTER TABLE user DROP COLUMN notify_by_email;