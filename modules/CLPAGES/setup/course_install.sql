CREATE TABLE IF NOT EXISTS `__CL_COURSE__clpages_pages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `authorId` INT(11) NOT NULL,
  `editorId` INT(11) NOT NULL,
  `creationTime` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `lastModificationTime` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  `displayMode` ENUM('PAGE','SLIDE') NOT NULL DEFAULT 'PAGE',
  PRIMARY KEY(id)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__clpages_contents` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `pageId` INT(11) NOT NULL,
  `type` VARCHAR(60) NOT NULL DEFAULT 'Text',
  `data` TEXT NOT NULL,
  `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  `titleVisibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  `rank` INT(11) NOT NULL default '0',
  PRIMARY KEY(id)
) TYPE=MyISAM;