ALTER TABLE posted_link ADD COLUMN `author_vendor_id` int(11) NOT NULL default '0';
/* We can't use FK as old ORM does not support nulls. */