
DROP TABLE IF EXISTS data_users;
CREATE TABLE IF NOT EXISTS data_users (
    id          INT(11) NOT NULL AUTO_INCREMENT,
    email       VARCHAR(50) NOT NULL,
    password    VARCHAR(100) NOT NULL,
    company     ENUM('VendorStack', 'company1','company2'),
    is_active   SMALLINT,
    is_admin    SMALLINT,
    PRIMARY KEY (id),
    UNIQUE KEY  email_unique (email)
);

INSERT INTO `data_users` (`id`, `email`, `password`, `company`, `is_active`, `is_admin`) VALUES
  (1, 'admin@vendorstack.com', '$2a$10$aVqhDXC4KgMixMKfF9txh.fIHylPvRYTbF2/CKu9ADp2H2gpx6XZu', 'VendorStack', 1, 1),
  (2, 'worker1@dataentry.com', '$2a$10$Xi44G6dUXs8SsIcamXgXSONoZpIEfwfPRKbuZxDe965QUkychxbhK', 'company1', 1, 0),
  (3, 'worker2@dataentry.com', '$2a$10$99dI7ID7FPbJ0yah6ScPUu5ggXXp1IZQapZUjKhtiaSF6TThVuvJC', 'company1', 1, 0),
  (4, 'worker3@dataentry.com', '$2a$10$Fr3lXwh7ttlPurDwvrEOT.EVhmxFqoV3WXipApK90gxFcvdvurV5S', 'company1', 1, 0),
  (5, 'bear@dslabs.lan', '$2a$10$aVqhDXC4KgMixMKfF9txh.fIHylPvRYTbF2/CKu9ADp2H2gpx6XZu', 'VendorStack', 1, 0),
  (6, 'beara@dslabs.lan', '$2a$10$aVqhDXC4KgMixMKfF9txh.fIHylPvRYTbF2/CKu9ADp2H2gpx6XZu', 'VendorStack', 1, 1);


DROP TABLE IF EXISTS data_vendors;
CREATE TABLE IF NOT EXISTS data_vendors (
    id          INT(11) NOT NULL AUTO_INCREMENT,
    type ENUM ('vendor', 'company') DEFAULT 'vendor',
    status ENUM('new', 'deleted', 'completed', 'ready', 'verified', 'exported') DEFAULT NULL,

    vendor_raw  VARCHAR(100) NOT NULL,
    vendor      VARCHAR(100) DEFAULT NULL,
    source      VARCHAR(100) NOT NULL,
    batch_id    VARCHAR(100) NOT NULL,

    is_duplicate    ENUM('name', 'url') DEFAULT NULL,
    is_crawled      SMALLINT DEFAULT 0,
    deleted_ts      TIMESTAMP NULL DEFAULT NULL,

    worker_id   SMALLINT DEFAULT NULL,
    work_start_ts TIMESTAMP NULL DEFAULT NULL,
    work_end_ts TIMESTAMP NULL DEFAULT NULL,

    crawled_google_url  VARCHAR(200) DEFAULT NULL,
    linkedin    VARCHAR(200) DEFAULT NULL,
    facebook    VARCHAR(200) DEFAULT NULL,
    twitter     VARCHAR(200) DEFAULT NULL,
    crunchbase  VARCHAR(200) DEFAULT NULL,
    angel       VARCHAR(200) DEFAULT NULL,

    crawled_google_url_source   VARCHAR(200) DEFAULT NULL,
    linkedin_source   VARCHAR(200) DEFAULT NULL,
    facebook_source   VARCHAR(200) DEFAULT NULL,
    twitter_source    VARCHAR(200) DEFAULT NULL,
    crunchbase_source VARCHAR(200) DEFAULT NULL,
    angel_source      VARCHAR(200) DEFAULT NULL,

    city        VARCHAR(100) DEFAULT NULL,
    country     VARCHAR(100) DEFAULT NULL,
    logo        VARCHAR(200) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    size        VARCHAR(100) DEFAULT NULL,
    indusrty    VARCHAR(100) DEFAULT NULL,

    city_source         VARCHAR(20) DEFAULT NULL,
    country_source      VARCHAR(20) DEFAULT NULL,
    logo_source         VARCHAR(20) DEFAULT NULL,
    description_source  VARCHAR(20) DEFAULT NULL,
    size_source  VARCHAR(20) DEFAULT NULL,
    industry_source  VARCHAR(20) DEFAULT NULL,

    logo_filename       VARCHAR(20) DEFAULT NULL,

    PRIMARY KEY (id)
);