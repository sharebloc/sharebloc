ALTER TABLE user DROP COLUMN `votes_count`;

UPDATE user SET confirm_email_key = CONCAT(MD5(RAND()), user_id), cookie_key=MD5(RAND())
WHERE f_contest_voter=1;