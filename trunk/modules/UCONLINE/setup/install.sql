CREATE TABLE IF NOT EXISTS `__CL_MAIN__user_online` (
    `id` INT(11) NOT NULL auto_increment,
    `user_id` INT(11) NOT NULL default '0',
    `last_action` DATETIME NOT NULL default '0000-00-00 00:00:00',
    `time_offset` INT(4) NOT NULL default '0',
    PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;