CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_user_data` (
    `user_id` int(11) NOT NULL,
    `noma` varchar(12) NOT NULL,
    `sigle_anet` varchar(32) NOT NULL,
    `other_data` text,
    `last_sync` datetime DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_class_data` (
    `class_name` varchar(255) NOT NULL,
    `last_sync` datetime DEFAULT '0000-00-00 00:00:00',
    `last_error` datetime DEFAULT '0000-00-00 00:00:00',
    `details` TEXT,
    PRIMARY KEY (`class_name`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__epc_log` (
    `id` INT(11) NOT NULL AUTOINCREMENT,
    `class_id` INT(11),
    `class_name` varchar(255) NOT NULL,
    `date` datetime DEFAULT '0000-00-00 00:00:00',
    `action` VARCHAR(32) NOT NULL,
    `course_id` VARCHAR(255),
    `user_id` INT(11) NOT NULL,
    `client_ip` VARCHAR(32) NOT NULL,
    `client_forwarded_ip` VARCHAR(32),
    `status` ENUM('log','success','error') NOT NULL,
    `message` TEXT,

    PRIMARY KEY (`id`),
    KEY(`class_id`),
    KEY(`class_name`),
    KEY(`date`),
    KEY(`course_id`),
    KEY(`user_id`),
    KEY(`status`)
) ENGINE=MyISAM;