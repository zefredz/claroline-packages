CREATE TABLE IF NOT EXISTS `__CL_COURSE__results_evaluations` (
  `evaluation_id` int(11) NOT NULL auto_increment,
  `titre` varchar(255) NOT NULL default '',
  `maximum` varchar(10) NOT NULL default '',
  `ponderation` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`evaluation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS`__CL_COURSE__results_entries` (
  `user_id` varchar(10) NOT NULL default '',
  `evaluation_id` int(11) NOT NULL default '0',
  `note` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`user_id`,`evaluation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;