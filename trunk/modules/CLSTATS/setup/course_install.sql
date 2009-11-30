CREATE TABLE IF NOT EXISTS `__CL_COURSE__courses_stats` (
  `code_course` varchar(255) NOT NULL,
  `code_display` varchar(255) NOT NULL,
  `dbName` varchar(255) NOT NULL,
  `folderName` varchar(255) NOT NULL,
  `status` enum('pending','done') NOT NULL DEFAULT 'pending'
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__stats` (
  `code_course` varchar(255) NOT NULL,
  `toolLabel` varchar(255) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `itemValue` int(11) NOT NULL,
  `dateCreation` int(11) NOT NULL
) ENGINE=MyISAM;