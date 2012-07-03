# $Id$
# $Revision$

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_session` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `context` ENUM('user','group') NOT NULL DEFAULT 'user',
    `type` VARCHAR(32) NOT NULL DEFAULT 'generic',
    `optionList` TEXT NOT NULL DEFAULT 'a:0:{}',
    `startDate` DATETIME,
    `endDate` DATETIME,
    `status` ENUM('open','close') NOT NULL default 'open',
    `rank` INT(3),
    `visibility` ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_slot` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `sessionId` INT(11) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `startDate` DATATIME,
    `availableSpace` INT(3) NOT NULL DEFAULT 1,
    `rank` INT(3),
    `visibility` ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__icsubscr_record` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `userId` INT(11),
    `groupId` INT(11),
    `sessionId` INT(11) NOT NULL,
    `slotId` INT(11) NOT NULL,
    `rank` INT(3),
    PRIMARY KEY(`id`)
) ENGINE=MyISAM;