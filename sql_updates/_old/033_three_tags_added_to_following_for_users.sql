INSERT INTO link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
SELECT user_id, 'user', 1, 'tag', 'follow', '2013-10-01 00:00:00' FROM user
WHERE NOT EXISTS
 (
  SELECT (1) FROM link
  WHERE entity1_type='user' AND entity1_id=user_id
  AND entity2_type='tag' AND entity2_id=1
 );

INSERT INTO link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
SELECT user_id, 'user', 5, 'tag', 'follow', '2013-10-01 00:00:00' FROM user
WHERE NOT EXISTS
 (
  SELECT (1) FROM link
  WHERE entity1_type='user' AND entity1_id=user_id
  AND entity2_type='tag' AND entity2_id=5
 );

INSERT INTO link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
SELECT user_id, 'user', 7, 'tag', 'follow', '2013-10-01 00:00:00' FROM user
WHERE NOT EXISTS
 (
  SELECT (1) FROM link
  WHERE entity1_type='user' AND entity1_id=user_id
  AND entity2_type='tag' AND entity2_id=7
 );

-- delete from link where date_added= '2013-10-01 00:00:00'