DROP TABLE group_permission;
DROP TABLE group_permission_review;
DROP TABLE group_permission_question;
DROP TABLE group_permission_comment;
DROP TABLE groups;

UPDATE user SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE user modify COLUMN privacy enum('public', 'anonymous') NOT NULL DEFAULT 'public';

UPDATE answer_back SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE answer_back modify COLUMN privacy enum('public', 'anonymous') NOT NULL DEFAULT 'public';

UPDATE comment SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE comment modify COLUMN privacy enum('public', 'anonymous') NOT NULL DEFAULT 'public';

UPDATE posted_link SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE posted_link modify COLUMN privacy enum('public', 'anonymous') NOT NULL DEFAULT 'public';

UPDATE question SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE question modify COLUMN privacy enum('public', 'anonymous') NOT NULL DEFAULT 'public';

UPDATE review SET privacy = 'anonymous' WHERE privacy = 'custom';
ALTER TABLE review modify COLUMN privacy enum('public', 'anonymous') 

delete from link where entity2_type = 'group' or entity1_type = 'group';