CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_user_data` (
  `user_id` int(11) NOT NULL,
  `noma` varchar(12) NOT NULL,
  `sigle_anet` varchar(32) NOT NULL,
  `other_data` text,
  `last_sync` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM

CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_class_data` (
  `class_name` varchar(255) NOT NULL,
  `last_sync` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`class_name`)
) ENGINE=MyISAM