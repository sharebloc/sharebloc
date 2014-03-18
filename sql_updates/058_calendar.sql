CREATE TABLE calendar (
    event_id int(11) NOT NULL auto_increment,
    name text NOT NULL DEFAULT '',
    url text NOT NULL DEFAULT '',
    start_date text default NULL,
    end_date text default NULL,
    start_ts datetime default NULL,
    end_ts datetime default NULL,
    location varchar(256) NOT NULL DEFAULT '',
    user_id int(11) NOT NULL default '0',
    added_ts datetime default NULL,
    deleted_ts datetime default NULL,
    f_approved tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY  (event_id),
    CONSTRAINT calendar_fk FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE calendar ADD COLUMN tag_id int(11) NOT NULL DEFAULT 1 AFTER location;
ALTER TABLE calendar ADD CONSTRAINT tag_fk FOREIGN KEY (tag_id) REFERENCES tag (tag_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tag_selection modify COLUMN entity_type enum('vendor','user','posted_link','question','review', 'event') NOT NULL DEFAULT 'vendor';
ALTER TABLE relation modify COLUMN entity_type enum('vendor','user','posted_link','question','review', 'event') NOT NULL DEFAULT 'vendor';

ALTER TABLE calendar ADD COLUMN f_only_month tinyint(1) NOT NULL DEFAULT 0 AFTER f_approved;