CREATE TABLE IF NOT EXISTS `__CL_COURSE__blog_posts` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  userId int(11) unsigned NOT NULL default '0',
  groupId int(11) unsigned NOT NULL default '0',
  title VARCHAR(255) NOT NULL DEFAULT '',
  chapo TEXT,
  contents TEXT,
  ctime datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__blog_comments` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  postId int(11) unsigned NOT NULL default '0',
  userId int(11) unsigned NOT NULL default '0',
  contents TEXT,
  ctime datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY(id)
) ENGINE=MyISAM;