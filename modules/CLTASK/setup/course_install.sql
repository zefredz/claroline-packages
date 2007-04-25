CREATE TABLE IF NOT EXISTS `__CL_COURSE__cltask_tasks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `startDate` datetime default NULL,
  `endDate` datetime default NULL,
  `dueDate` datetime default NULL,
  `description` text,
  `priority` tinyint(4) unsigned default NULL,
  `progress` tinyint(4) unsigned default NULL,
  `visibility` ENUM('SHOW', 'HIDE') DEFAULT 'SHOW' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;