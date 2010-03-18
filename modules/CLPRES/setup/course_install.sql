CREATE TABLE IF NOT EXISTS `__CL_COURSE__clpres_attendance` (
  `id` int(11) NOT NULL auto_increment,
  `date_att` VARCHAR(10) NOT NULL default '',
  `user_id` int(11) NOT NULL,
  `is_att` tinyint NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1;
