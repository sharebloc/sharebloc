
DELETE FROM also_viewed WHERE entity_type='group';
UPDATE also_viewed SET entity_type='vendor' WHERE entity_type='company';

DELETE FROM claim WHERE entity_type='group';
UPDATE claim SET entity_type='vendor' WHERE entity_type='company';

DELETE FROM data_backup_vendors WHERE type='group';
UPDATE data_backup_vendors SET type='vendor' WHERE type='company';

DELETE FROM data_vendors WHERE type='group';
UPDATE data_vendors SET type='vendor' WHERE type='company';

DELETE FROM link WHERE entity1_type='group';
UPDATE link SET entity1_type='vendor' WHERE entity1_type='company';

DELETE FROM link WHERE entity2_type='group';
UPDATE link SET entity2_type='vendor' WHERE entity2_type='company';

DELETE FROM screenshot WHERE entity_type='group';
UPDATE also_viewed SET entity_type='vendor' WHERE entity_type='company';

UPDATE screenshot SET entity_type='vendor' WHERE entity_type='company';

DELETE FROM stats WHERE entity_type='group';
UPDATE stats SET entity_type='vendor' WHERE entity_type='company';

DELETE FROM tag WHERE tag_type='group';
UPDATE tag SET tag_type='vendor' WHERE tag_type='company';

DELETE FROM tag_selection WHERE entity_type='group';
UPDATE tag_selection SET entity_type='vendor' WHERE entity_type='company';

DELETE FROM tag_selection WHERE tag_type='group';
UPDATE tag_selection SET tag_type='vendor' WHERE tag_type='company';


ALTER TABLE also_viewed modify COLUMN entity_type enum('vendor','question') NOT NULL;
ALTER TABLE claim modify COLUMN entity_type enum('vendor','question') NOT NULL;
ALTER TABLE data_backup_vendors modify COLUMN type enum('vendor') NOT NULL DEFAULT 'vendor';
ALTER TABLE data_vendors modify COLUMN type enum('vendor') NOT NULL DEFAULT 'vendor';
ALTER TABLE link modify COLUMN entity1_type enum('vendor','user') NOT NULL DEFAULT 'vendor';
ALTER TABLE link modify COLUMN entity2_type enum('vendor','user', 'tag') NOT NULL DEFAULT 'vendor';
ALTER TABLE screenshot modify COLUMN entity_type enum('vendor','user') NOT NULL DEFAULT 'vendor';
ALTER TABLE stats modify COLUMN entity_type enum('vendor','user','question','posted_link','comment') NOT NULL DEFAULT 'vendor';
ALTER TABLE tag modify COLUMN tag_type enum('vendor','user','vendor_platform','vendor_cost') NOT NULL DEFAULT 'vendor';
ALTER TABLE tag_selection modify COLUMN entity_type enum('vendor','user','posted_link','question','review') NOT NULL DEFAULT 'vendor';
ALTER TABLE tag_selection modify COLUMN tag_type enum('vendor','user','vendor_platform','vendor_cost') NOT NULL DEFAULT 'vendor';
