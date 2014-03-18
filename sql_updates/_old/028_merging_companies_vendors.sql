-- exec this before execution of converter script

drop table answer_back;
delete from recache;

ALTER TABLE vendor ADD COLUMN `company_size` enum('1','2-10','11-50','51-200','201-500','501-1000','1001-5000','5001-10000','10000+','unknown') NOT NULL default 'unknown' AFTER website;

ALTER TABLE vendor DROP INDEX code_name_idx;
ALTER TABLE vendor ADD UNIQUE KEY `code_name_idx` (`code_name`);

ALTER TABLE company ADD COLUMN `vendor_id` int(11) NULL;


delete from also_viewed where counter=0;

delete a from also_viewed as a
left join company  as b on  entitya_id=company_id or entityb_id=company_id
where company_id is null
and entity_type='company';

delete a from also_viewed as a
left join vendor  as b on  entitya_id=vendor_id or entityb_id=vendor_id
where vendor_id is null
and entity_type='vendor';

-- exec converter script

update tag set tag_type='vendor' where tag_type='company';
update tag_selection set tag_type='vendor' where tag_type='company';

drop table company;

-- now review is in relations table too
ALTER TABLE relation modify COLUMN entity_type enum('question','comment','posted_link', 'review') NOT NULL default 'question';
insert into relation (entity_id, entity_type, vendor_id, is_review, date_added, date_modified)
select review_id, 'review', vendor_id, 0, now(), now() from review;
-- debug
-- delete from relation where entity_type ='';

update link set link_type='follow' where entity1_type='user';

--https://vendorstack.atlassian.net/browse/VEN-300
delete from link where entity1_type!='user';