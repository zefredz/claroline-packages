# $Id$
# $Revision$

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_sessions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL default '',
    `description` TEXT NOT NULL,
    `context` ENUM('USER','GROUP') NOT NULL DEFAULT 'USER',
    `type` ENUM('UNIQUE', 'MULTIPLE') NOT NULL DEFAULT 'UNIQUE',
    `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
    `lock` ENUM('OPEN','CLOSE') NOT NULL default 'OPEN',
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_slots` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `subscriptionId` INT(11) NOT NULL default 0,
    `title` VARCHAR(255) NOT NULL default '',
    `description` TEXT NOT NULL,
    `space_available` INT(3) NOT NULL default 0,
    `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_subscribers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` ENUM('USER','GROUP') NOT NULL DEFAULT 'USER',
    `type_id` INT(11) NOT NULL default 0,
    PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__subscr_slots_subscribers` (
    `slotId` INT(11) NOT NULL default 0,
    `subscriberId` INT(11) NOT NULL default 0,
    `subscriptionId` INT(11) NOT NULL default 0,
    PRIMARY KEY (`slotId`, `subscriptionId`, `subscriberId`)
) TYPE = MyISAM;