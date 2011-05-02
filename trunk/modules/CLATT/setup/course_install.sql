CREATE TABLE IF NOT EXISTS `__CL_COURSE__attendance` (
  `id_list` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance` enum('present','partial','absent','late','excused') NOT NULL,
  `comment` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id_list`,`user_id`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__attendance_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_att` datetime NOT NULL,
  `title` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=1;