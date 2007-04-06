CREATE TABLE IF NOT EXISTS __CL_MAIN__agenda2_cours_events (
	`id` int(11) NOT NULL auto_increment,
	`cours_id` varchar(200),
	`starthour` time NOT NULL default '00:00:00',
	`startday` date NOT NULL default '0000-00-00',
	`endhour` time NOT NULL default '00:00:00',
	`endday` date NOT NULL default '0000-00-00',
	`title` varchar(200),
	`content` text,
	`author` varchar(200),
	`type` varchar(200),
	`visibility` enum('SHOW','HIDE') NOT NULL default 'SHOW',
PRIMARY KEY (`id`)
) TYPE=MyISAM;

CREATE TABLE `__CL_MAIN__agend2_shared_events` (
	`id` int(11) NOT NULL auto_increment,
	`user_id` int(11) NOT NULL,
	`starthour` time NOT NULL default '00:00:00',
	`startday` date NOT NULL default '0000-00-00',
	`endhour` time NOT NULL default '00:00:00',
	`endday` date NOT NULL default '0000-00-00',
	`title` varchar(200),
	`content` text,
	`author` varchar(200),
	`type` varchar(200),
PRIMARY KEY (`id`)
)TYPE= MYISAM ;

CREATE TABLE `__CL_MAIN__agend2_events_type` (
	`id` int(11) NOT NULL auto_increment,
	`type` varchar(200),
PRIMARY KEY (`id`)
)TYPE = MYISAM ;
