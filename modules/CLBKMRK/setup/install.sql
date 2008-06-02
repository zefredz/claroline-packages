CREATE TABLE IF NOT EXISTS `__CL_MAIN__clbkmrk_bookmarks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `owner_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;