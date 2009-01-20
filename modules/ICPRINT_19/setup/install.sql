CREATE TABLE IF NOT EXISTS `__CL_MAIN__icprint_documents`(
	document_id INT(11) NOT NULL AUTO_INCREMENT,
	document_title VARCHAR(255) NOT NULL,
	document_localpath VARCHAR(255) NOT NULL,
	document_globalpath VARCHAR(255) NOT NULL,
	document_publisher INT(11) NOT NULL,
	document_course_id VARCHAR(40) NOT NULL,
	document_hash VARCHAR(255) NOT NULL,
	document_length INT(11) NOT NULL,
	PRIMARY KEY(document_id)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__icprint_actions`(
	action_id INT(11) NOT NULL AUTO_INCREMENT,
	action_document_id INT(11) NOT NULL,
	action_name ENUM('add', 'delete','modify','coursedelete') NOT NULL,
	action_course_id VARCHAR(40) NOT NULL,
	action_user_id INT(11) NOT NULL,
	action_timestamp DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	action_document_localpath VARCHAR(255) NOT NULL,
	action_document_hash VARCHAR(255) NOT NULL,
	PRIMARY KEY(action_id)
) TYPE=MyISAM;

#CREATE TABLE IF NOT EXISTS `__CL_MAIN__Keyring_services`(
#	serviceName VARCHAR(255) NOT NULL,
#    serviceHost VARCHAR(255) NOT NULL,
#    serviceKey  VARCHAR(255) NOT NULL,
#    PRIMARY KEY(serviceName,serviceHost)
#) Type=MYISAM;