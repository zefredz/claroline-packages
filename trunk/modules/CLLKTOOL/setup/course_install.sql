CREATE TABLE IF NOT EXISTS `__CL_COURSE__cllktool_links`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255),
    url TINYTEXT NOT NULL,
    type ENUM('post:json','post:xml','post:plain','widget','iframe','popup') NOT NULL DEFAULT 'iframe',
    options TEXT NULL,
    visibility ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    PRIMARY KEY(id)
);