# $Id$
# $Revision$

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_session` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `context` ENUM('user','group') NOT NULL DEFAULT 'user',
    `type` ENUM('undated','dated','timeslot') NOT NULL DEFAULT 'undated',
    `optionList` TEXT,
    `openingDate` DATETIME,
    `closingDate` DATETIME,
    `is_open` TINYINT(1) NOT NULL DEFAULT 1,
    `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_slot` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL,
    `startDate` DATETIME,
    `endDate` DATETIME,
    `availableSpace` INT(3) NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_record` (
    `userId` INT(11) NOT NULL,
    `groupId` INT(11),
    `slotId` INT(11) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_list` (
    `itemId` INT(11) NOT NULL,
    `parentId` INT(11) NOT NULL DEFAULT 0,
    `itemType` ENUM('session','slot','record') NOT NULL DEFAULT 'session',
    `rank` INT(3) NOT NULL DEFAULT 1,
    PRIMARY KEY(`itemId`,`itemType`)
) ENGINE=MyISAM;