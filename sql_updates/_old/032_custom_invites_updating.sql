ALTER TABLE user ADD COLUMN `invited_by` int(11) NOT NULL DEFAULT 0;


CREATE TABLE invite_custom_sent (
    inv_id int(11) NOT NULL AUTO_INCREMENT,
    first_name varchar(128) NULL DEFAULT NULL,
    last_name varchar(128) NULL DEFAULT NULL,
    email varchar(128) NOT NULL,
    text text,
    created_ts datetime NOT NULL,
    user_id int(11) NOT NULL,
    PRIMARY KEY  (inv_id)
);

/*sql to update the table*/
UPDATE invite_custom_sent SET first_name=NULL where first_name='';
UPDATE invite_custom_sent SET last_name=NULL where last_name='';
