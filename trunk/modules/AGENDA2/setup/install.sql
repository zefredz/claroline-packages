CREATE TABLE IF NOT EXISTS __CL_MAIN__event (
	`id` int(11) NOT NULL auto_increment,
	`title` varchar(200),
	`description` text,
	`author_id` int(11),
	`start_date` datetime NOT NULL default '0000-00-00 00:00:00',
	`end_date` datetime NOT NULL default '0000-00-00 00:00:00',
	`master_event_id` int(11),
PRIMARY KEY (`id`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS`__CL_MAIN__rel_event_recipient` (
	`id` int(11) NOT NULL auto_increment,
	`event_id` int(11),
	`user_id` int(11),
	`cours_id` varchar(200),
	`group_id` int(11),
	`visibility` enum('SHOW','HIDE') NOT NULL default 'SHOW',
PRIMARY KEY (`id`)
)TYPE= MYISAM ;
