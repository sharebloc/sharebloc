DROP TABLE vendor_request;
DROP TABLE user_email_settings;
DROP TABLE logo_old_back;
DROP TABLE invite;
DROP TABLE email_settings;
DROP TABLE data_al_vendors;

delete from recache;

INSERT INTO sharebloc_join (email, created_ts, info)
(
    SELECT email, request_date, concat('{"ip":"', ip_address, '"}')
    FROM signup_request
);

DROP TABLE signup_request;

SELECT *
FROM sharebloc_join
JOIN user ON user.email = sharebloc_join.email

-- execute after check above
-- DELETE sharebloc_join
-- FROM sharebloc_join
-- JOIN user ON user.email = sharebloc_join.email;


ALTER TABLE sharebloc_join CHANGE COLUMN `email` `email` varchar(200) NOT NULL;
ALTER TABLE sharebloc_join DROP KEY `email`;

select * from sharebloc_join sb
join sharebloc_join t on sb.email=t.email and t.id<sb.id;

-- execute after check above
-- delete sharebloc_join sb from sharebloc_join sb
-- join sharebloc_join t on sb.email=t.email and t.id<sb.id;

ALTER TABLE sharebloc_join ADD UNIQUE KEY `email_idx` (`email`);

RENAME TABLE sharebloc_join TO join_requests;
