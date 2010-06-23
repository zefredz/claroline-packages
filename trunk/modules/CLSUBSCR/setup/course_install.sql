# $Id$
# $Revision$

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_sessions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL default '',
    `description` TEXT NOT NULL,
    `context` ENUM('user','group') NOT NULL DEFAULT 'user',
    `type` ENUM('unique', 'multiple') NOT NULL DEFAULT 'unique',
    `visibility` ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    `lock` ENUM('open','close') NOT NULL default 'open',
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_slots` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `subscriptionId` INT(11) NOT NULL default 0,
    `title` VARCHAR(255) NOT NULL default '',
    `description` TEXT NOT NULL,
    `availableSpace` INT(3) NOT NULL default 0,
    `visibility` ENUM('visible','invisible') NOT NULL DEFAULT 'visible',
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_subscribers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` ENUM('user','group') NOT NULL DEFAULT 'user',
    `typeId` INT(11) NOT NULL default 0,
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_slots_subscribers` (
    `slotId` INT(11) NOT NULL default 0,
    `subscriberId` INT(11) NOT NULL default 0,
    `subscriptionId` INT(11) NOT NULL default 0,
    PRIMARY KEY (`slotId`, `subscriptionId`, `subscriberId`)
) TYPE = MyISAM;