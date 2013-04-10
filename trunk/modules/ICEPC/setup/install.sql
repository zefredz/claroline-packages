CREATE TABLE IF NOT EXISTS `cl_epc_user_data` (
  `user_id` int(11) NOT NULL,
  `noma` varchar(12) NOT NULL,
  `sigle_anet` varchar(32) NOT NULL,
  `other_data` text,
  `last_sync` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM