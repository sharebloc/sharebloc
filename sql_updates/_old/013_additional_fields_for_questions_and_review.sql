ALTER TABLE question ADD COLUMN `question_title` TEXT NULL AFTER user_id;

ALTER TABLE review ADD COLUMN `advice` TEXT NULL AFTER status;
ALTER TABLE review ADD COLUMN `reason` TEXT NULL AFTER status;
ALTER TABLE review ADD COLUMN `review_title` TEXT NULL AFTER status;
