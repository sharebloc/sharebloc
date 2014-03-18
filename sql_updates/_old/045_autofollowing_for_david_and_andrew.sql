-- select for check

select entity1_id, 'user', (select user_id from user WHERE code_name='david_cheng'), 'user', 'follow', NOW()
from link
where link_type='follow' and entity1_type='user' and entity2_type='user'
	and entity2_id=(select user_id from user WHERE code_name='david_cheng')
	and entity1_id not in (
		select entity2_id as following from link
		where link_type='follow' and entity1_type='user' and entity2_type='user'
        	and entity1_id=(select user_id from user WHERE code_name='david_cheng')
      );

select entity1_id, 'user', (select user_id from user WHERE code_name='andrew_koller'), 'user', 'follow', NOW()
from link
where link_type='follow' and entity1_type='user' and entity2_type='user'
	and entity2_id=(select user_id from user WHERE code_name='andrew_koller')
	and entity1_id not in (
		select entity2_id as following from link
		where link_type='follow' and entity1_type='user' and entity2_type='user'
        	and entity1_id=(select user_id from user WHERE code_name='andrew_koller')
      );


-- insert

insert into link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
select (select user_id from user WHERE code_name='david_cheng'), 'user', entity1_id, 'user', 'follow', NOW()
from link
where link_type='follow' and entity1_type='user' and entity2_type='user'
	and entity2_id=(select user_id from user WHERE code_name='david_cheng')
	and entity1_id not in (
		select entity2_id as following from link
		where link_type='follow' and entity1_type='user' and entity2_type='user'
        	and entity1_id=(select user_id from user WHERE code_name='david_cheng')
      );

insert into link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
select (select user_id from user WHERE code_name='andrew_koller'), 'user', entity1_id, 'user', 'follow', NOW()
from link
where link_type='follow' and entity1_type='user' and entity2_type='user'
	and entity2_id=(select user_id from user WHERE code_name='andrew_koller')
	and entity1_id not in (
		select entity2_id as following from link
		where link_type='follow' and entity1_type='user' and entity2_type='user'
        	and entity1_id=(select user_id from user WHERE code_name='andrew_koller')
      );