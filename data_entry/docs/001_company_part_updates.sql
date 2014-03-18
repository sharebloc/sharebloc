ALTER TABLE data_vendors ADD COLUMN type ENUM ('vendor', 'company') DEFAULT 'vendor' AFTER id;

ALTER TABLE data_vendors ADD COLUMN size VARCHAR(100) DEFAULT NULL AFTER description;
ALTER TABLE data_vendors ADD COLUMN industry VARCHAR(100) DEFAULT NULL AFTER size;

ALTER TABLE data_vendors ADD COLUMN size_source VARCHAR(20) DEFAULT NULL AFTER description_source;
ALTER TABLE data_vendors ADD COLUMN industry_source VARCHAR(20) DEFAULT NULL AFTER size_source;
