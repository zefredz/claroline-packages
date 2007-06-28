CREATE TABLE IF NOT EXISTS `__CL_MAIN__skype_course` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `course_id` VARCHAR(40) NOT NULL,
  `skype_name` VARCHAR(255) default '',
  PRIMARY KEY(`id`)
) TYPE = MyISAM;

