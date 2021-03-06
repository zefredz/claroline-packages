# mastery_score ?
# score_max & score_min ?
# attempts last_item ?

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_path` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL default '',
  `description` TEXT NOT NULL,
  `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  `rank` INT(11) NOT NULL default '0',
  `version` ENUM('scorm12','scorm13') NOT NULL default 'scorm12',
  `lock` enum('OPEN','CLOSE') NOT NULL default 'OPEN',
  `identifier` VARCHAR(255) default '',
  `allow_reinit` ENUM('YES', 'NO') NOT NULL DEFAULT 'YES',
  `view_mode` ENUM('EMBEDDED', 'FULLSCREEN') NOT NULL DEFAULT 'EMBEDDED',
  `encoding` VARCHAR(12) NOT NULL DEFAULT 'UTF-8',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `path_id` INT(11) NOT NULL,
  `type` ENUM('CONTAINER','MODULE','SCORM','GRAPPLE') NOT NULL DEFAULT 'MODULE',
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  `rank` INT(11) NOT NULL default '0',
  `identifier` VARCHAR(255) default '',
  `sys_path` VARCHAR(255) default '',
  `parent_id` INT(11),
  `previous_id` INT(11),
  `next_id` INT(11),
  `launch_data` text NOT NULL default '',
  `timeLimitAction` ENUM( 'exit,message', 'exit,no message', 'continue,message', 'continue,no message' ) NOT NULL DEFAULT 'continue,no message',
  `completionThreshold` VARCHAR(6) NOT NULL default '',
  `branchConditions` text NULL,
  `redirectBranchConditions` enum('0','1') NOT NULL DEFAULT '0',
  `newWindow` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_attempt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `path_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `last_item_id` INT(11),
  `progress` INT(11),
  `attempt_number` INT(11),
  PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_item_attempt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `attempt_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `location` varchar(255) NOT NULL default '',
  `completion_status` enum('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN') NOT NULL default 'NOT ATTEMPTED',
  `entry` enum('AB-INITIO','RESUME','') NOT NULL default 'AB-INITIO',
  `score_raw` tinyint(4) NOT NULL default '-1',
  `score_min` tinyint(4) NOT NULL default '-1',
  `score_max` tinyint(4) NOT NULL default '-1',
  `total_time` varchar(13) NOT NULL default '0000:00:00.00',
  `session_time` varchar(13) NOT NULL default '0000:00:00.00',
  `suspend_data` text NOT NULL,
  `credit` enum('CREDIT','NO-CREDIT') NOT NULL default 'NO-CREDIT',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_item_blockcondition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `cond_item_id` int(11) NOT NULL DEFAULT '0',
  `completion_status` enum('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN') NOT NULL,
  `operator` enum('=') NOT NULL,
  `condition` enum('-1','AND','OR') NOT NULL,
  `raw_to_pass` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__lp_grapple_resources` (
  `grappleId` varchar(255) NOT NULL,
  `uri` text,
  `name` text,
  `path` text,
  PRIMARY KEY (`grappleId`)
) ENGINE=MyISAM;