ALTER TABLE user ADD COLUMN notify_daily tinyint(1) NOT NULL DEFAULT 1 AFTER notify_weekly;
ALTER TABLE user ADD COLUMN notify_suggestion tinyint(1) NOT NULL DEFAULT 1 AFTER notify_contest;