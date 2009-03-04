CREATE TABLE IF NOT EXISTS `__CL_COURSE__icpcrdr_podcasts`(
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255),
    url TINYTEXT NOT NULL,
    visibility ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    PRIMARY KEY(id)
);