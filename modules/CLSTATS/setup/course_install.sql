CREATE TABLE IF NOT EXISTS `__CL_MAIN__courses_stats` (
  `code_course` varchar(255) NOT NULL,
  `code_display` varchar(255) NOT NULL,
  `dbName` varchar(255) NOT NULL,
  `folderName` varchar(255) NOT NULL,
  `status` enum('pending','done') NOT NULL DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT;


CREATE TABLE IF NOT EXISTS `__CL_MAIN__stats` (
  `code_course` varchar(255) NOT NULL,
  `toolLabel` varchar(255) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `itemValue` int(11) NOT NULL,
  `dateCreation` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__stats_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `toolLabel` varchar(255) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `max` int(11) NOT NULL DEFAULT '0',
  `average` int(11) NOT NULL DEFAULT '0',
  `zero` int(11) NOT NULL DEFAULT '0',
  `one` int(11) NOT NULL DEFAULT '0',
  `two` int(11) NOT NULL DEFAULT '0',
  `three` int(11) NOT NULL DEFAULT '0',
  `four` int(11) NOT NULL DEFAULT '0',
  `five` int(11) NOT NULL DEFAULT '0',
  `moreFive` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__stats_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM;