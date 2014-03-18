INSERT INTO tag (tag_name, tag_type, description, parent_tag_id, date_added, code_name)
VALUES ('Accounting', 'vendor', '', 0, NOW(), 'accounting');

UPDATE tag
SET tag_name='Finance',
    code_name='finance'
WHERE code_name='accounting__finance';

DELETE FROM tag WHERE code_name='accounting' AND parent_tag_id != 0;
DELETE FROM tag WHERE code_name='finance' AND parent_tag_id != 0;

INSERT INTO link (entity1_id, entity1_type, entity2_id, entity2_type, link_type, date_added)
SELECT entity1_id, 'user', (SELECT tag_id FROM tag WHERE code_name='accounting'), 'tag', 'follow', NOW()
FROM link
WHERE entity1_type='user'
    AND entity2_id=(SELECT tag_id FROM tag WHERE code_name='finance')
    AND entity2_type='tag';
