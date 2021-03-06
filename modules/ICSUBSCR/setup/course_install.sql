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
    `is_open` BOOLEAN NOT NULL DEFAULT TRUE,
    `is_visible` BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_slot` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `sessionId`INT(11) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `startDate` DATETIME,
    `endDate` DATETIME,
    `availableSpace` INT(3) NOT NULL DEFAULT 0,
    `is_visible` BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_record` (
    `sessionId` INT(11) NOT NULL,
    `slotId` INT(11) NOT NULL,
    `userId` INT(11) NOT NULL,
    `groupId` INT(11),
    PRIMARY KEY(`userId`,`groupId`,`slotId`),
    UNIQUE KEY(`groupId`,`slotId`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_list` (
    `listId`INT(11) NOT NULL DEFAULT 0,
    `itemType` ENUM('session','slot') NOT NULL DEFAULT 'session',
    `itemId` INT(11) NOT NULL,
    `rank`INT(3) NOT NULL DEFAULT 1,
    PRIMARY KEY(`listId`,`itemId`)
) ENGINE=MyISAM;