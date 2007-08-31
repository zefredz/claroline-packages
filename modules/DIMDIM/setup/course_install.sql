CREATE TABLE IF NOT EXISTS `__CL_COURSE__dim_conference` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL default '',
  `description` TEXT NOT NULL,
  `waitingArea` ENUM('ENABLE','DISABLE') NOT NULL DEFAULT 'DISABLE',
  `maxUsers` INT(11) NOT NULL default '20',
  `duration` INT(11) NOT NULL default '1',
  `type` enum('AUDIO','AUDIOVIDEO') NOT NULL default 'AUDIO',
  `attendees` INT(11) NOT NULL default '0',
  `network` ENUM('DIALUP', 'CABLEDSL', 'LAN') NOT NULL DEFAULT 'DIALUP',
  `startTime` DATETIME NOT NULL default now(),
  PRIMARY KEY(`id`)
) TYPE = MyISAM;
