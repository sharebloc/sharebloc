ALTER TABLE company ADD COLUMN `added_by` INT (11) NOT NULL DEFAULT 0 AFTER date_added;
ALTER TABLE company ADD COLUMN `modified_by` INT (11) NOT NULL DEFAULT 0 AFTER date_modified;

ALTER TABLE vendor ADD COLUMN `added_by` INT (11) NOT NULL DEFAULT 0 AFTER date_added;
ALTER TABLE vendor ADD COLUMN `modified_by` INT (11) NOT NULL DEFAULT 0 AFTER date_modified;


CREATE TABLE temp AS
    SELECT user_id AS uid, b.company_id as cid FROM user
    JOIN company b ON b.company_id = user.company_id;

UPDATE company SET
    added_by = (SELECT uid FROM temp
                WHERE cid=company_id LIMIT 1);

DROP Table temp;

UPDATE company SET modified_by = added_by;