CREATE TABLE `__CL_MAIN__CLFDsubscription` (
  `id` int(11) NOT NULL auto_increment,
  `course_code` varchar(30) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `intro_text` text NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `max_users` int(6) default NULL,
  `title` varchar(250) NOT NULL,
  `allow_modification` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `__CL_MAIN__CLFDsubscriptionUser` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscription_date` datetime NOT NULL,
  PRIMARY KEY  (`subscription_id`,`user_id`)
) ENGINE=MyISAM;

CREATE TABLE `__CL_MAIN__CLFDsuscriptionIncompat` (
  `session_id` int(11) NOT NULL,
  `incompatible_session_id` int(11) NOT NULL,
  `course_code` varchar(30) NOT NULL,
  PRIMARY KEY  (`session_id`,`incompatible_session_id`,`course_code`)
) ENGINE=MyISAM;