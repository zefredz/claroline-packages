CREATE TABLE IF NOT EXISTS `__CL_MAIN__oovoo_course` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `course_id` VARCHAR(40) NOT NULL,
  `username` VARCHAR(255) DEFAULT '',
  PRIMARY KEY(`id`)
) TYPE = MyISAM;

